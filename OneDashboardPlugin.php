<?php

namespace Craft;

/**
 * One Dashboard Plugin.
 *
 * Set one dashboard for all users.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Itmundi
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://www.itmundi.nl
 */
class OneDashboardPlugin extends BasePlugin
{
    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('One Dashboard');
    }

    /**
     * Get plugin version.
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.1';
    }

    /**
     * Get plugin developer.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Bob Olde Hampsink';
    }

    /**
     * Get plugin developer url.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://www.itmundi.nl';
    }

    /**
     * Init dashboard check early.
     */
    public function init()
    {
        // Don't run when in console
        if (!craft()->isConsole()) {

            // Get current user
            $currentUser = craft()->userSession->getUser();

            // Make sure we're logged in onto CP
            if ($currentUser && $currentUser->can('accessCp') && craft()->request->isCpRequest()) {

                // Check if this user already has widgets
                $userWidgetRecords = WidgetRecord::model()->ordered()->findAllByAttributes(array(
                    'userId' => $currentUser->id,
                ));

                if (!$userWidgetRecords) {

                    // Get enabled admin widgets
                    $adminWidgetRecords = WidgetRecord::model()->ordered()->findAllByAttributes(array(
                        'userId' => 1,
                        'enabled' => 1,
                    ));

                    // Populate widget models
                    $adminWidgets = WidgetModel::populateModels($adminWidgetRecords);

                    // Loop through widget models
                    foreach ($adminWidgets as $widget) {

                        // Clear widget id
                        $widget->id = null;

                        // Save on user
                        craft()->dashboard->saveUserWidget($widget);
                    }
                }
            }
        }
    }
}
