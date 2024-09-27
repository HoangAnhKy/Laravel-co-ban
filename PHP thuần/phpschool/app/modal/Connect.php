<?php

class Connect
{

    private function ConnectDB()
    {
        $connect = mysqli_connect(SERVER, USER, PASSWORD, DATABASE);
        $connect->set_charset("utf8");
        return $connect;
    }

    public function select($sql, $modal = "", $contain = [])
    {
        $connect = $this->ConnectDB();
        $response = $connect->query($sql);
        $connect->close();
        $result = [];

        if ($modal !== "" && file_exists(__DIR__ . "/" . $modal . ".php")) {
            $modal = "\\modal\\" . $modal;
        }

        foreach ($response as $row => $each) {
            if ($modal !== "") {
                $result[$row] = new $modal($each);
                if (!empty($contain)){
                    foreach ($contain as $key){
                        if (isset($each[$key])){
                            $modal_contain = "\\modal\\" . ucfirst($key);
                            $result[$row]->$key = new $modal_contain(json_decode($each[$key], true)[0]);
                        }
                    }
                }

            } else {
                $result[$row] = $each;
            }
        }

        return $result;
    }
    public function selectOne($sql, $modal = "")
    {
        $connect = $this->ConnectDB();
        $response = $connect->query($sql);
        $connect->close();

        $result = null;

        if ($modal !== "" && file_exists(__DIR__ . "/" . $modal . ".php")) {
            $modal = "\\modal\\" . $modal;
        }
        if ($modal !== "") {
            $result = new $modal($response->fetch_assoc());
        } else {
            $result = $response->fetch_assoc();
        }
        return $result;
    }

    public function exec($sql)
    {
        $flag = false;
        $connect = $this->ConnectDB();
        if ($connect->query($sql)) {
            $flag = true;
        }
        $connect->close();
        return $flag;
    }
}