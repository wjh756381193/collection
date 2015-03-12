<?php 
/**
 *��������֪��γ��֮��ľ���,��λΪ��
 * @param float $lng1 ��һ��ľ���
 * @param float $lat1 ��һ���ά��
 * @param float $lng2 �ڶ���ľ���
 * @param float $lat2 �ڶ����ά��
 * @return boolean С���幫��ʱ����true����֮����false;
 * @author wjh
 */
function getDistance($lng1=0.0,$lat1=0.0,$lng2=0.0,$lat2=0.0){
    //���Ƕ�תΪ����
    $radLat1=deg2rad($lat1); //deg2rad()�������Ƕ�ת��Ϊ����
    $radLat2=deg2rad($lat2);
    $radLng1=deg2rad($lng1);
    $radLng2=deg2rad($lng2);
    $a=$radLat1-$radLat2;
    $b=$radLng1-$radLng2;
    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
    return $s<5000?true:false;
}