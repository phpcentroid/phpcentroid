<?php
namespace PHPCentroid\Data;

use PHPCentroid\Common\Application;

class DataContext extends DataContextBase
{
    private Application $application;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Gets the application instance of the data context
     * @return Application
     */
    public function getApplication(): Application {
        return $this->application;
    }

    /**
     * Sets the application instance of the data context
     * @param Application $application
     * @return void
     */
    public function setApplication(Application $application): void {
        $this->application = $application;
    }

    public function getModel(string $name): ?DataModelBase {
        // get model properties
        $model = $this->getApplication()->services->get(DataConfiguration::class)->getModel($name);
        if ($model) {
            // set context
            $model->setContext($this);
            // return model
            return $model;
        }
        return NULL;
    }

}