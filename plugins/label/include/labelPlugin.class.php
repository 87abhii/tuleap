<?php
/**
 * Copyright (c) Enalean, 2017-2018. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

use Tuleap\Dashboard\Project\ProjectDashboardController;
use Tuleap\Label\REST\ResourcesInjector;
use Tuleap\Label\Widget\Dao;
use Tuleap\Label\Widget\ProjectLabeledItems;
use Tuleap\Layout\IncludeAssets;
use Tuleap\Project\Label\MergeLabels;
use Tuleap\Project\Label\RemoveLabel;
use Tuleap\Request\CurrentPage;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/constants.php';

class labelPlugin extends Plugin
{
    public function __construct($id)
    {
        parent::__construct($id);
        $this->setScope(self::SCOPE_SYSTEM);

        bindtextdomain('tuleap-label', __DIR__.'/../site-content');
    }

    public function getHooksAndCallbacks()
    {
        $this->addHook(\Tuleap\Widget\Event\GetProjectWidgetList::NAME);
        $this->addHook(\Tuleap\Widget\Event\GetWidget::NAME);
        $this->addHook(Event::REST_RESOURCES);
        $this->addHook(Event::REST_PROJECT_RESOURCES);
        $this->addHook(RemoveLabel::NAME);
        $this->addHook(MergeLabels::NAME);
        $this->addHook(Event::BURNING_PARROT_GET_STYLESHEETS);
        $this->addHook(Event::BURNING_PARROT_GET_JAVASCRIPT_FILES);

        return parent::getHooksAndCallbacks();
    }

    /**
     * @return Tuleap\Label\Plugin\PluginInfo
     */
    public function getPluginInfo()
    {
        if (!$this->pluginInfo) {
            $this->pluginInfo = new Tuleap\Label\Plugin\PluginInfo($this);
        }
        return $this->pluginInfo;
    }

    public function getProjectWidgetList(\Tuleap\Widget\Event\GetProjectWidgetList $event)
    {
        $event->addWidget(ProjectLabeledItems::NAME);
    }

    public function widgetInstance(\Tuleap\Widget\Event\GetWidget $get_widget_event)
    {
        switch ($get_widget_event->getName()) {
            case ProjectLabeledItems::NAME:
                $get_widget_event->setWidget(new ProjectLabeledItems());
                break;
        }
    }

    /**
     * @see Event::REST_RESOURCES
     */
    public function restResources(array $params)
    {
        $injector = new ResourcesInjector();
        $injector->populate($params['restler']);
    }

    /**
     * @see Event::REST_PROJECT_RESOURCES
     */
    public function restProjectResources(array $params)
    {
        $injector = new ResourcesInjector();
        $injector->declareProjectResource($params['resources'], $params['project']);
    }

    public function removeLabel(RemoveLabel $event)
    {
        $this->getDao()->removeLabelById($event->getLabelToDeleteId());
    }

    public function mergeLabel(MergeLabels $merge_labels)
    {
        $this->getDao()->mergeLabelInTransaction(
            $merge_labels->getLabelToEditId(),
            $merge_labels->getLabelIdsToMerge()
        );
    }

    /**
     * @return Dao
     */
    private function getDao()
    {
        return new Dao();
    }

    /**
     * @see Event:BURNING_PARROT_GET_STYLESHEETS
     */
    public function burningParrotGetStylesheets(array $params)
    {
        if ($this->isInDashboard()) {
            $variant = $params['variant'];
            $params['stylesheets'][] = $this->getThemePath() .'/css/style-'. $variant->getName() .'.css';
        }
    }

    /**
     * @see Event::BURNING_PARROT_GET_JAVASCRIPT_FILES
     */
    public function burningParrotGetJavascriptFiles(array $params)
    {
        if ($this->isInDashboard()) {
            $assets = new IncludeAssets(LABEL_BASE_DIR . '/www/assets', LABEL_BASE_URL . '/assets');

            $params['javascript_files'][] = $assets->getFileURL('configure-widget.js');
        }
    }

    private function isInDashboard()
    {
        $current_page = new CurrentPage();

        return $current_page->isDashboard();
    }
}
