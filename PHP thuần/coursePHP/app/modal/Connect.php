<?php

namespace modal;
class Connect
{
    private function connectDB()
    {
        $connect = mysqli_connect(SERVER, USER, PASSWORD, DATABASE);
        $connect->set_charset("utf8mb4");
        return $connect;
    }


    public function seclect($sql, $modal = "")
    {
        $connect = $this->connectDB();
        $response = $connect->query($sql);
        $connect->close();

        $result = [];
        if ($modal !== "" && file_exists(__DIR__ . "/" . $modal . ".php")) {
            $modal = "\\modal\\" . $modal;
        }
        foreach ($response as $each) {
            if ($modal !== "") {
                $result[$each["id"]] = new $modal($each);
            } else {
                $result[] = $each;
            }
        }
        return $result;
    }

    public function exec($sql)
    {
        $connect = $this->connectDB();
        $connect->query($sql);
        $connect->close();
    }
}