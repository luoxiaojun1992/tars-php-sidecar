<?php

namespace HttpServer\controller;

use GuzzleHttp\Client;
use HttpServer\component\Controller;
use HttpServer\config\ENVConf;

class SidecarController extends Controller
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionRoute()
    {
        $request = $this->getRequest();

        $request_uri = $request->data['server']['request_uri'];
        $host = $request->data['header']['host'];
        $request_method = $request->data['server']['request_method'];
        $post = $request->data['post'];
        $files = $request->data['files'];

//        $this->sendRaw(print_r($request->data, true));

        $backend_request_uri = str_replace('/Sidecar/route', '', $request_uri) ?: '/';

        $route_config = ENVConf::get('route');
        $route = null;
        if (isset($route_config[$host][$backend_request_uri])) {
            $route = $route_config[$host][$backend_request_uri];
        } elseif (isset($route_config[$host]['*'])) {
            $route = $route_config[$host]['*'];
        }

        if ($route) {
            $options = [
                'headers' => $request->data['header'],
            ];
            if (isset($route['timeout'])) {
                $options['timeout'] = $route['timeout'];
            }
            if ($post) {
                if (is_array($post)) {
                    $options['form_params'] = $post;
                } else {
                    $options['body'] = $post;
                }
            }
            if ($files) {
                foreach ($files as $form_key => $file) {
                    $options['multipart'][] = [
                        'name' => $form_key,
                        'contents' => fopen($file['tmp_name'], 'r'),
                        'filename' => $file['name'],
                    ];
                }
            }
            $res = (new Client())->request(
                strtoupper($request_method),
                trim($route['host'], '/') . $backend_request_uri,
                $options
            );

            $this->status($res->getStatusCode());
            foreach ($res->getHeaders() as $header_name => $values) {
                foreach ($values as $value) {
                    $this->header($header_name, $value);
                }
            }
            $this->sendRaw($res->getBody()->getContents());
        } else {
            $this->sendRaw('');
        }
    }
}
