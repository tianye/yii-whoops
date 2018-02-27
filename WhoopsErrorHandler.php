<?php

use Whoops\Run as Whoops;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;

class WhoopsErrorHandler extends CErrorHandler
{

    /**
     * Whoops instance.
     *
     * @var Whoops
     */
    protected $whoops;

    /**
     * Page title in case of non-AJAX requests.
     *
     * @var string
     */
    public $pageTitle = '错误调试';

    /**
     * Instantiate Whoops with the correct handlers.
     */
    public function __construct()
    {
        $this->whoops = new Whoops;

        if (Yii::app()->request->isAjaxRequest) {
            $this->whoops->pushHandler(new JsonResponseHandler);
        } else {
            $page_handler = new PrettyPageHandler;
            $page_handler->setPageTitle($this->pageTitle);
            $page_handler->addDataTable('BUG随时提醒你', [
                '1.上线要关DEBUG.'       => '',
                '2.上线之前要好好检查代码' => '',
                '3.请注意编码的规范'      => '',
                '4.请认真检测代码逻辑'    => '',
                'Request information' => static::createRequestTable(),
            ]);
            $this->whoops->pushHandler($page_handler);
        }
    }

    protected static function createRequestTable()
    {
        $request = [];
        $header  = [];
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $header[] = $_SERVER['SERVER_PROTOCOL'];
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $header[] = $_SERVER['REQUEST_METHOD'];
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $header[] = $_SERVER['HTTP_HOST'];
        }
        $request['Request'] = implode('  ', $header);

        if (isset($_SERVER['REQUEST_URI'])) {
            $request['Resource'] = ltrim($_SERVER['REQUEST_URI'], '/');
        }

        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $request['Entry script'] = $_SERVER['SCRIPT_FILENAME'];
        }

        $ips = [];
        if (isset($_SERVER['SERVER_ADDR'])) {
            $ips[] = 'Server: ' . $_SERVER['SERVER_ADDR'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ips[] = 'Client: ' . $_SERVER['REMOTE_ADDR'];
        }
        $request['IPs'] = implode('  ||  ', $ips);

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $request['User agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        if (isset($_SERVER['REQUEST_TIME'])) {
            $request['Request time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }

        return $request;
    }

    /**
     * Forwards an error to Whoops.
     *
     * @param CErrorEvent $event
     */
    protected function handleError($event)
    {
        $exception = new ErrorException($event->message, $event->code, E_ERROR, $event->file, $event->line);
        $this->handleException($exception);
        //$this->whoops->handleError($event->code, $event->message, $event->file, $event->line);
    }

    /**
     * Forwards an exception to Whoops.
     *
     * @param Exception $exception
     */
    protected function handleException($exception)
    {
        if ($exception instanceof Exception) {
            $this->whoops->handleException($exception);
        } elseif ($exception instanceof Error) {
            $exception = new ErrorException($exception->getMessage(), $exception->getCode(), E_ERROR, $exception->getFile(), $exception->getLine(), $exception->getPrevious());
            $this->whoops->handleException($exception);
        }
    }

}