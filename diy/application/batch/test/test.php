<?PHP
    $base='[{"name":"\u8987\u8005\u306e\u5854\uff0835\u968e"},{"name":"\u8987\u8005\u306e\u5854\uff0836\u968e"},{"name":"\u8987\u8005\u306e\u5854\uff0837\u968e"},{"name":"\u8987\u8005\u306e\u5854\uff0838\u968e"},{"name":"\u8987\u8005\u306e\u5854\uff0839\u968e"},{"name":"\u8987\u8005\u306e\u5854\uff0840\u968e"}]';
    $base = json_decode($base,true);
die(var_dump($base));
//    die(var_dump($base));
    $data = array(  array('name'        => '覇者の塔（35階'
                          ),
                    array('name'        => '覇者の塔（36階'
                          ),
                    array('name'        => '覇者の塔（37階'
                          ),
                    array('name'        => '覇者の塔（38階'
                          ),
                    array('name'        => '覇者の塔（39階'
                          ),
                    array('name'        => '覇者の塔（40階'
                          ),
                 );
    $data = json_encode($data);
    echo $data."\n";
?>
