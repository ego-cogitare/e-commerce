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
            $params = $request->getParams();

            switch ($args['action']) {
                case 'get':
                    $settings = \Models\Settings::fetchAll([
                        'key' => [
                            '$in' => [
                                'currencyList',
                                'currencyCource',
                                'currencyCode',
                                'productStates',
                                'homeSlider',
                            ]
                        ]
                    ])->toArray();

                    return $response->write(
                        json_encode($this->convertToKeyVal($settings))
                    );
                break;

                case 'set':
                  $setting =  \Models\Settings::fetchOne([
                    'key' => 'homeSlider'
                  ]);

                  if (empty($setting)) {
                    $setting = new \Models\Settings();
                    $setting->key = 'homeSlider';
                  }

                  $setting->value = json_encode($params['data']);
                  $setting->save();

                  return $response->write(
                      json_encode([ 'success' => true ])
                  );
                break;
            }
        }
    }
