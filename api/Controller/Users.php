<?php
namespace API;

class Users
{
    public static function login(array $data)
    {
        $valedator = [
            'user' => 'string,min:5',
            'password' => 'string'
        ];
        $valed_data = \NI_request::API_validate($data, $valedator);
        $valed_data['password'] = sha1($valed_data['password']);
        $user = \model\Users::MultAuth($valed_data);
        \NI_Api::$response['status'] = 200;
        if (empty($user)) {
            \NI_Api::$response['data'] = [
                'msg' => 'wrong username or password'
            ];
            return;
        } else {
            \NI_Api::$response['data'] = [
                'msg' => 'login sucssfully',
                'token' => \NI_JWT::CreateToken(['username' => $user->username,'password' => $user->password]),
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'talk' => $user->talk
                ]
                
            ];
            return;
        }
    }

    public static function register(array $data)
    {
        $valedator = [
            'username' => 'string,min:5',
            'email' => 'email',
            'talk' => 'bool',
            'password' => 'string,min:8'
        ];
        $valed_data = \NI_request::API_validate($data, $valedator);
        $valed_data['password'] = sha1($valed_data['password']);
        $user = \model\Users::Uniqregister($valed_data);

        if ($user) {
            $token = \NI_JWT::CreateToken($valed_data);
            unset($valed_data['password']);
            \NI_Api::$response['status'] = 200;
            \NI_Api::$response['data'] = [
                'msg'=> 'user register sucssfully',
                'token' => $token,
                'key' => 'user',
                'data' => $valed_data
            ];
            return;
        } else {
            \NI_Api::$response['status'] = 404;
            \NI_Api::$response['data'] = [
                'msg'=> 'data muste nuiqe',
                'data' => $valed_data
            ];
            return;
        }
    }
    /*
        public static function createProfile(array $data)
        {
            $valedator = [
                'user_id' => 'int',
                'dob' => 'date',
                'weight' => 'int,min:40',
                'height' => 'int,min:50'
            ];
            $valed_data = \NI_request::API_validate($data, $valedator);
            if (!empty(\model\Profiles::findUserid($valed_data['user_id']))) {

                self::updateProfile($data);
                return;
            }
            if (!\NI_JWT::UserAuth() || $valed_data['user_id'] != \NI_JWT::UserAuth()[1]) {
                \NI_Api::$response['status'] = 404;
                \NI_Api::$response['data'] = [
                    'msg' => 'token not valed'
                ];
                return;
            } else {
                $profile = \model\Profiles::create($valed_data);
                if ($profile) {
                    \NI_Api::$response['status'] = 200;
                    \NI_Api::$response['data'] = [
                        'msg' => 'profile create sucssfully',
                        'data' => $valed_data ?? null
                    ];
                    return;
                } else {
                    \NI_Api::$response['status'] = 404;
                    \NI_Api::$response['data'] = [
                        'msg' => 'something went wrong',
                        'data' => $valed_data ?? null
                    ];
                    return;
                }
            }
        }

        public static function updateProfile(array $data)
        {
            $valedator = [
                'user_id' => 'int',
                'dob' => 'date',
                'weight' => 'int,min:40',
                'height' => 'int,min:50'
            ];
            $valed_data = \NI_request::API_validate($data, array_intersect_key($valedator, $data));
            if (!isset($valed_data['user_id'])) {
                \NI_Api::$response['status'] = 404;
                \NI_Api::$response['data'] = [
                    'msg' => 'user_id not valid'
                ];
                return;
            }
            if (empty(\model\Profiles::findUserid($valed_data['user_id']))) {
                var_dump('yes');
                self::createProfile($data);
            }
            if (!\NI_JWT::UserAuth() || $valed_data['user_id'] != \NI_JWT::UserAuth()[1]) {
                \NI_Api::$response['status'] = 404;
                \NI_Api::$response['data'] = [
                    'msg' => 'token not valed'
                ];
                return;
            } else {
                $profile = \model\Profiles::update($valed_data);
                if ($profile) {
                    \NI_Api::$response['status'] = 200;
                    \NI_Api::$response['data'] = [
                        'msg' => 'profile update sucssfully',
                        'data' => $valed_data ?? null
                    ];
                    return;
                } else {
                    \NI_Api::$response['status'] = 404;
                    \NI_Api::$response['data'] = [
                        'msg' => 'something went wrong',
                        'data' => $valed_data ?? null
                    ];
                    return;
                }
            }
        }

        */
}
