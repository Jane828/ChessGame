<?php

class Account_CONST {
    /*****************************
     * 参数
     *****************************/

    const Default_Avatar_Url = "";

    //设备类型
    const Client_Type_IOS     = 0;
    const Client_Type_Android = 1;
    const Client_Type_Web     = 2;
    const Client_Type_PC      = 3;

    //强制下线操作码
    const Operation_ForceLogout = "ForceLogout-1";


    /*
        二维码组装
    */
    const QRCode_Url_WebLogin = "https://ft.fexteam.com/wl/";


}


/* End of file constants.php */
/* Location: ./application/config/constants.php */