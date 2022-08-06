<?php
class Common
{
    public static function didReceiveRequest()
    {
        AlefCore::onCheckAuthorization(function ($user_id)
        {
			//$user = q1("select * from users where id=:user_id and is_active=1", ["user_id"=>$user_id]);
            //return !empty($user);
            return true;
        });
    }
}