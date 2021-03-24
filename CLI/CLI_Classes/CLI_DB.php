<?php
class CLI_DB
{
    public static function MysqlConn()
    {
        $conn = @new mysqli(HOST, USER, PASS);
        if ($conn->connect_error) {
            echo "\e[0;31;40m DB Connection Error: \e[0m \e[0;35m $conn->connect_error \e[0m\n";
            exit;
        } else {
            return $conn;
        }
    }
    public static function PDOConn()
    {
        echo "\e[1;34;40m trying to connction to creat tables \e[0m\n";
        $dns = "mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DBNAME;
        try {
            $conn = new PDO($dns, USER, PASS, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            ]);
            echo "\e[1;33;40m Connect successfully \e[0m\n";
            return $conn;
        } catch (PDOException $e) {
            echo "\e[0;31;40m DB Connection Error: \e[0m \n \e[0;35m";
            print_r($e->getMessage());
            echo " \e[0m \n";
            exit;
        }
    }

    public static function CreateDataBase()
    {
        echo "\e[1;33;40m reading connction data \e[0m\n";

        echo "\e[1;34;40m trying to connction frist time to creat database: \e[0m \e[0;32m " . DBNAME . " \e[0m\n";
        $conn = self::MysqlConn();

        $sql = "CREATE SCHEMA " . DBNAME . " CHARACTER SET utf8 COLLATE utf8_general_ci";
        if ($conn->query($sql) === true) {
            echo "\e[1;33;40m Database:\e[0m \e[0;32m " . DBNAME . " \e[0m \e[1;33;40m Created successfully \e[0m\n";
        } else {
            echo "\e[0;31;40m DB Creating Error: \e[0m \e[0;35m $conn->error \e[0m\n";
        }

        if ($conn->close()) {
            echo "\e[1;33;40m connection close successfully \e[0m\n";
        }
    }

    public static function CreateTables()
    {
        $conn = self::PDOConn();
        if (!$conn) {
            exit;
        }
        $tableArray = CLI_Helper::ReadCLITableFile();
        if (empty($tableArray)) {
            exit;
        }
        if (DeleteFlag == true) {
            $DefaultColumn = '
        id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
        create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        delete_flag INT( 1 ) DEFAULT 0 NOT NULL,
        create_from VARCHAR( 255 ) NULL,
        delete_from VARCHAR( 255 ) NULL,
        update_from VARCHAR( 255 ) NULL
        ';
        } else {
            $DefaultColumn = '
            id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
            create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            create_from VARCHAR( 255 ) NULL,
            delete_from VARCHAR( 255 ) NULL,
            update_from VARCHAR( 255 ) NULL
            ';
        }
        foreach ($tableArray as $key => $value) {
            $value = $value . $DefaultColumn;
            $createTable = $conn->prepare("CREATE TABLE IF NOT EXISTS " . DBNAME . '.' . $key . " ($value)COLLATE='utf8_general_ci'");
            $createTable->execute();
            if ($createTable) {
                echo "Table \e[0;33;40m $key \e[0m - \e[0;32m Created! \e[0m \n";
            } else {
                echo "Table $key not successfully created! \n";
            }
        }
    }

    public static function InsertIntoDB()
    {
        $conn = self::PDOConn();
        if (!$conn) {
            exit;
        }
        $seeds = CLI_Helper::ReadCLISeedssFile();
        if (empty($seeds)) {
            exit;
        }

        foreach ($seeds as $TableName => $TableArray) {
            if (empty($TableArray)) {
                exit;
            }
            $bind = ':' . implode(',:', array_keys($TableArray[0]));
            foreach ($TableArray as $columnAndValue) {
                foreach ($columnAndValue as $keyn => $valuen) {
                    $query1 = "SELECT COUNT(" . $keyn . ") FROM $TableName WHERE $keyn='" . $valuen . "'";
                    $s1 = $conn->prepare($query1);
                    $s1->execute();
                    if ($s1->fetchColumn() == 0) {
                        $sql = "INSERT INTO $TableName (" . implode(',', array_keys($columnAndValue)) . ") VALUES ($bind)";
                        $stmt = $conn->prepare($sql);
                        $stmtTrue = $stmt->execute(array_combine(explode(',', $bind), array_values($columnAndValue)));
                        if ($stmtTrue) {
                            echo "'$keyn' => '$valuen' done\n";
                            break;
                        } else {
                            echo "some thing went wrong in '$keyn' => '$valuen' \n";
                            break;
                        }
                    } else {
                        echo "'$keyn' => '$valuen' alredy exist \n";
                        break;
                    }
                }
            }
        }
    }

    public static function CreateRelation(array $input)
    {
        if (isset($input[2]) && isset($input[3]) && isset($input[4]) && strpos($input[2], '.')) {
            $Table1Arr = explode('.', $input[2]);
            $Table1 = $Table1Arr[0];
            $Table2 = $input[3];
            $Table1Column = $Table1Arr[1];
            $master_id = implode('_', $Table1Arr);

            $s1 = str_replace("[update=", '', $input[4]);
            $s2 = str_replace("delete=", '', $s1);
            $s3 = str_replace("]", '', $s2);
            $Update8Delete_stats = explode(',', $s3);
            $up_st = $Update8Delete_stats[0];
            $d_st = $Update8Delete_stats[1];

            $query = "ALTER TABLE `$Table2` ADD $master_id INT";
            $query2 = "ALTER TABLE `$Table2` ADD CONSTRAINT $master_id FOREIGN KEY (`$master_id`) REFERENCES `$Table1` (`$Table1Column`) ON DELETE $d_st ON UPDATE $up_st";

            $conn = self::PDOConn();
            if (!$conn) {
                exit;
            }

            if (count($conn->query("SHOW COLUMNS FROM `$Table2` LIKE '$master_id'")->fetchAll())) {
                try {
                    $createRelation = $conn->prepare($query2);
                    $createRelation->execute();
                    echo "Relation \e[0;33;40m $input[2] >> $input[3] \e[0m - \e[0;32m Created! \e[0m \n";
                    echo "warning \e[0;33;40m Dont Forget edit MOdel And Controller \e[0m - \e[0;32m Manually! \e[0m \n";
                } catch (PDOException $ex) {
                    echo "Relation $input[2] >> $input[3] not successfully created! \n";
                    echo  $ex->getMessage();
                }
            } else {
                try {
                    $createTable = $conn->prepare($query);
                    $createTable->execute();
                    try {
                        $createRelation = $conn->prepare($query2);
                        $createRelation->execute();
                        echo "Relation \e[0;33;40m $input[2] >> $input[3] \e[0m - \e[0;32m Created! \e[0m \n";
                        echo "warning \e[0;33;40m Dont Forget edit MOdel And Controller \e[0m - \e[0;32m Manually! \e[0m \n";
                    } catch (PDOException $ex) {
                        echo "Relation $input[2] >> $input[3] not successfully created! \n";
                        echo  $ex->getMessage();
                    }
                } catch (PDOException $ex) {
                    echo "Relation $input[2] >> $input[3] not successfully created! \n";
                    echo  $ex->getMessage();
                }
            }
        } else {
            $myfile = fopen(RelationFile, "r") or die("Unable to open file!");
            $str = str_replace("\n", "", fread($myfile, filesize(RelationFile)));
            $command = explode(';', $str);
            array_pop($command);
            fclose($myfile);
            foreach ($command as $key) {
                system("php NI.php make relation $key");
            }
        }
    }
}
