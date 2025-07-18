<?php
namespace axenox\BDT\Behat\TwigFormatter\Context;

use axenox\BDT\Behat\Common\ScreenshotRegistry;
use axenox\BDT\Behat\TwigFormatter\Formatter\BehatFormatter;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Class BehatFormatterContext
 *
 * @package axenox\BDT\Behat\TwigFormatter\Context
 */
class BehatFormatterContext extends MinkContext implements SnippetAcceptingContext
{
    private $currentScenario;
    protected static $currentSuite;

    /**
     * @BeforeFeature
     *
     * @param BeforeFeatureScope $scope
     *
     */
    public static function setUpScreenshotSuiteEnvironment4ElkanBehatFormatter(BeforeFeatureScope $scope)
    {
        self::$currentSuite = $scope->getSuite()->getName();
    }

    /**
     * @BeforeScenario
     */
    public function setUpScreenshotScenarioEnvironmentElkanBehatFormatter(BeforeScenarioScope $scope)
    {
        $this->currentScenario = $scope->getScenario();
    }

    /**
     * Take screen-shot when step fails.
     * Take screenshot on result step (Then)
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     * @param AfterStepScope $scope
     */
    public function afterStepScreenShotOnFailure(AfterStepScope $scope)
    {

          /* TODO how to check, which driver can take screenshots?
            if (!$driver instanceof Selenium2Driver) {
                return;
            }*/
             

        if(!$scope->getTestResult()->isPassed()) {
            $fileName = $this->buildScreenshotFilename(
                $scope->getSuite()->getName(),
                $scope->getFeature()->getFile(),
                $scope->getStep()->getLine()
            );
    
            $temp_destination = getcwd().DIRECTORY_SEPARATOR.".tmp_behatFormatter";
            if (!is_dir($temp_destination)) {
                mkdir($temp_destination, 0777, true);
            }
    
            error_log("Taking screenshot for failed step: " . $scope->getStep()->getText());
            ScreenshotRegistry::setScreenshotName($fileName);
            $this->saveScreenshot($fileName, $temp_destination);
        }
    }
    
    public function buildScreenshotFilename(string $suiteName, string $featureFilePath, int $featureLine): string
    {
        return $suiteName
            . "." . str_replace('.feature', '', basename($featureFilePath))
            . "." . $featureLine
            . "." . date("YmdHis")
            . ".png";
    }
}