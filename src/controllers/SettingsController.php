<?php
    namespace Controllers;

    class SettingsController
    {
        private function convertToKeyVal(array $settings)
        {
            $keyVal = [];
            foreach ($settings as $setting) {
                $keyVal[$setting['key']] = $setting['value'];
            }
            return $keyVal;
        }

        public function __invoke($request, $response, $args)
        {
            switch ($args['action']) {
                case 'get':
                    $settings = \Models\Settings::fetchAll([
                        'key' => [
                            '$in' => [
                                'currencyList',
                                'currencyCource',
                                'currencyCode',
                                'productStates'
                            ]
                        ]
                    ])->toArray();

                    return $response->write(
                        json_encode($this->convertToKeyVal($settings))
                    );
                break;
            }
        }
    }
