<?php 
/*	
	���
    https_request�Ƿ���������д��һ������΢�Žӿ����ݴ�������ܺ�����������Ӧ������΢�Žӿ����ݵķ��ʼ��ύ��
    ��ԭ����ʹ��curlʵ����΢�Ź���ƽ̨�ӿ�http��httpsЭ��ʱ��get��post��ʽ��
    ����ʵ������
*/
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /*
		����
		���Զ���˵��Ŀ���Ϊ����ʹ������
	*/
    $access_token = "";
    $jsonmenu = '{
          "button":[
          {
                "name":"����Ԥ��",
               "sub_button":[
                {
                   "type":"click",
                   "name":"��������",
                   "key":"��������"
                },
                {
                   "type":"click",
                   "name":"�Ϻ�����",
                   "key":"�����Ϻ�"
                },
                {
                   "type":"click",
                   "name":"��������",
                   "key":"��������"
                },
                {
                   "type":"click",
                   "name":"��������",
                   "key":"��������"
                },
                {
                    "type":"view",
                    "name":"��������",
                    "url":"http://m.hao123.com/a/tianqi"
                }]
          

           },
           {
               "name":"����������",
               "sub_button":[
                {
                   "type":"click",
                   "name":"��˾���",
                   "key":"company"
                },
                {
                   "type":"click",
                   "name":"Ȥζ��Ϸ",
                   "key":"��Ϸ"
                },
                {
                    "type":"click",
                    "name":"����Ц��",
                    "key":"Ц��"
                }]
           

           }]
     }';
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
    $result = https_request($url, $jsonmenu);
    var_dump($result);
?>