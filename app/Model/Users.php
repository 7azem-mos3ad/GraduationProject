<?php
    namespace model;

    class Users
    {
        public static function create(array $data)
        {
            $new = \ORM::for_table("Users")->create();
                        
                
            $new->username = $data["username"];
                            
            $new->email = $data["email"];
                                                        
            $new->password = $data["password"];
                            
            $new->talk = $data["talk"];
                            
                            
            if ($new->save()) {
                return true;
            } else {
                return false;
            }
        }

        public static function update(array $data)
        {
            $update = \ORM::for_table("Users")->find_one([$data["id"]]);
            if (is_bool($update)) {
                return false ;
            }
                        
            foreach ($data as $key => $value) {
                if ($key == "id") {
                    continue;
                }
                $update->set($key, $value);
            }
            if ($update->save()) {
                return true;
            } else {
                return false;
            }
        }

        public static function select()
        {
            return \ORM::for_table("Users")->findArray();
        }

        public static function find(int $id)
        {
            return \ORM::for_table("Users")->find_one([$id])->as_array();
        }
                    
        public static function delete(int $id)
        {
            $delete = \ORM::for_table("Users")->find_one([$id]);
            if (is_bool($delete)) {
                return false ;
            }
            if ($delete->delete()) {
                return true;
            } else {
                return false;
            }
        }
                
                
        public static function MultAuth(array $data)
        {
            $check = \ORM::for_table("Users")->where_any_is(
                array(
                array("username" => $data["username"], "password" => $data["password"]),
                array("email" => $data["username"], "password" => $data["password"]),array("phone" => $data["username"], "password" => $data["password"]),
                )
            )->find_one();

            return empty($check) ? false : $check ;
        }
        
        public static function uniqregister(array $data)
        {
            $check = \ORM::for_table("Users")->where_any_is(
                array(
                array("username" => $data["username"]),array("email" => $data["email"]),array("phone" => $data["phone"]),
                )
            )->find_one();

            if (empty($check)) {
                return (self::create($data)) ;
            } else {
                return false;
            }
        }
        
        public static function uniqe(array $data)
        {
            $check = \ORM::for_table("Users")->where_any_is(
                array(
                array("username" => $data["username"]),array("email" => $data["email"]),array("phone" => $data["phone"])
                )
            )->where_not_equal("id", $data["id"])->find_one();

            if (empty($check)) {
                return true;
            } else {
                return false;
            }
        }
    }
