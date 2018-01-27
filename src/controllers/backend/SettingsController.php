<?php
    namespace Controllers\Backend;

    class SettingsController
    {
        const SETTINGS = [
            'currencyList',
            'currencyCode',
            'delivery',
            'payment',
            'homeSlider',
        ];
        
        private function convertToKeyVal(array $settings)
        {
            $keyVal = [];
            foreach ($settings as $setting) {
                $keyVal[$setting['key']] = $setting['value'];
            }
            foreach (self::SETTINGS as $key) {
                if (!isset($keyVal[$key])) {
                    $keyVal[$key] = "[]";
                }
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
                            '$in' => self::SETTINGS
                        ]
                    ])->toArray();

                    return $response->write(
                        json_encode($this->convertToKeyVal($settings))
                    );
                break;

                case 'set':
                    if (!in_array($params['key'], self::SETTINGS)) {
                        return $response->withStatus(400)->write(
                            json_encode(['error'=>'Недопустимый ключ: ' . $params['key']])
                        );
                    }

                    $setting =  \Models\Settings::fetchOne([
                        'key' => $params['key']
                    ]);

                    if (empty($setting)) {
                        $setting = new \Models\Settings();
                        $setting->key = $params['key'];
                    }

                    $setting->value = $params['data'];
                    $setting->save();

                    return $response->write(
                        json_encode(['success' => true])
                    );
                break;
            }
        }
    }
