Auto-update resources from the PHP Micro-Framework HLEB
=====================

### ! This library is only for updates.

 If you need to install the framework, use the link: [github.com/phphleb/hleb](https://github.com/phphleb/hleb) 
 
 A distinctive feature of the micro-framework HLEB is the minimalism of the code and the speed of work. The choice of this framework allows you to launch a full-fledged product with minimal time costs and appeals to documentation; it is easy, simple and fast.
 At the same time, it solves typical tasks, such as routing, shifting actions to controllers, model support, so, the basic MVC implementation. This is the very minimum you need to quickly launch an application.
 
 
 ##U_P_D_A_T_E_R
 Install using Composer:
 ```bash
 $ composer require phphleb/updater
 ```

Library connection samples in files 'start.php', 'add_sample.php' and 'remove_sample.php'.

Test run from the root directory of the HLEB project:

 ```bash
 $ php console phphleb/updater
 ```

Sample package installation:

 ```bash
 $ composer php console phphleb/updater --add
 ```

 ```bash
 $ composer dump-autoload
 ```

Delete common files:

 ```bash
 $ php console phphleb/updater --remove
 ```