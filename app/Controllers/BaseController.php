<?php

namespace App\Controllers;

use App\Models\SettingModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'text', 'number'];

    /**
     * Loaded global settings
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * SettingModel instance
     *
     * @var SettingModel
     */
    protected SettingModel $settingModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload settings
        $this->settingModel = new SettingModel();
        $this->settings     = $this->settingModel->getAllSettings();
    }
}
