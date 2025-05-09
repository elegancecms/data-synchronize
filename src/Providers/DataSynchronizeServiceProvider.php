<?php

namespace EleganceCMS\DataSynchronize\Providers;

use EleganceCMS\Base\Facades\DashboardMenu;
use EleganceCMS\Base\Facades\PanelSectionManager as PanelSectionManagerFacade;
use EleganceCMS\Base\Supports\ServiceProvider;
use EleganceCMS\Base\Traits\LoadAndPublishDataTrait;
use EleganceCMS\DataSynchronize\Commands\ClearChunksCommand;
use EleganceCMS\DataSynchronize\Commands\ExportCommand;
use EleganceCMS\DataSynchronize\Commands\ExportControllerMakeCommand;
use EleganceCMS\DataSynchronize\Commands\ExporterMakeCommand;
use EleganceCMS\DataSynchronize\Commands\ImportCommand;
use EleganceCMS\DataSynchronize\Commands\ImportControllerMakeCommand;
use EleganceCMS\DataSynchronize\Commands\ImporterMakeCommand;
use EleganceCMS\DataSynchronize\PanelSections\ExportPanelSection;
use EleganceCMS\DataSynchronize\PanelSections\ImportPanelSection;
use Illuminate\Console\Scheduling\Schedule;

class DataSynchronizeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('packages/data-synchronize')
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['data-synchronize'])
            ->loadAndPublishViews()
            ->publishAssets()
            ->registerPanelSection()
            ->registerDashboardMenu();

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImporterMakeCommand::class,
                ExporterMakeCommand::class,
                ImportControllerMakeCommand::class,
                ExportControllerMakeCommand::class,
                ClearChunksCommand::class,
                ExportCommand::class,
                ImportCommand::class,
            ]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
                $schedule
                    ->command(ClearChunksCommand::class)
                    ->dailyAt('00:00');
            });
        }
    }

    protected function getPath(?string $path = null): string
    {
        return __DIR__ . '/../..' . ($path ? '/' . ltrim($path, '/') : '');
    }

    protected function registerPanelSection(): self
    {
        PanelSectionManagerFacade::group('data-synchronize')->beforeRendering(function () {
            PanelSectionManagerFacade::default()
                ->register(ExportPanelSection::class)
                ->register(ImportPanelSection::class);
        });

        return $this;
    }

    protected function registerDashboardMenu(): self
    {
        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-packages-data-synchronize',
                    'parent_id' => 'cms-core-tools',
                    'priority' => 9000,
                    'name' => 'packages/data-synchronize::data-synchronize.tools.export_import_data',
                    'icon' => 'ti ti-package-import',
                    'route' => 'tools.data-synchronize',
                ]);
        });

        return $this;
    }
}
