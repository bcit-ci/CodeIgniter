.. _CodeIgniter 專案: https://codeigniter.com/download
.. _CodeIgniter:      https://github.com/gamearming/CodeIgniter/
.. _index.php:        /index.php
.. _application:      /application
.. _views:            /application/views/
.. _system:           /system
.. _組態設定檔:        /application/config/config.php
.. _資料庫設定檔:      /application/config/database.php
.. _資料庫組態設定:    /user_guide_src/source/database/configuration.rst

#########################
安裝說明
#########################

CodeIgniter 請按以下四個步驟來進行安裝：

1. 下載 `CodeIgniter 專案`_ 並解壓縮。
2. 上傳 Codeigniter_ 目錄下的所有檔案到您的主機，一般來說 index.php_ 會在主機的根目錄。
3. 組態設定檔_   
  - 使用文字編輯器開啟 組態設定檔_，找到 ``$config['base_url']`` = '您的網址';。     
  - 如果想要加密或使用通信期( **sessions** )，找到 ``$config['encryption_key']`` = '您的密鑰';。

4. 如果要使用資料庫，則開啟 資料庫設定檔_，編輯您的 `資料庫組態設定`_。

系統安全性
=========================
預設是在所有的資料夾都放 *.htaccess* 檔案來避免直接存取，但有些主機並不支援 *.htaccess* 檔案，所以還是將 web 根目錄上的 system_ 以及 application_ 二個目錄移動到瀏覽器無法直接存取的位置，才是最安全的做法。

如果您希望隱藏 Codeigniter_ 目錄來增加安全性，則可以重新命名 system_、application_。

如果您希望可以存取 views_ 資料夾，那麼請將其從 application_ 資料夾移出。

當改變 system_、application_ 以及 views_ 資料夾，都必須在根目錄的 index.php_ 檔案中找到 ``$system_path`` = ``'system';`` 以及 ``$application_folder`` = ``'application';`` 以及 ``$view_folder`` = ''; 變數並設定成您希望的目錄名稱。

以上路徑設定最好是完整的路徑，例如： '*/www/MyUser/system*'.。

另外在產品發佈時，CodeIgniter_ 預設取消 PHP 錯誤訊息及自訂函數。

您可以在根目錄 index.php_ 檔案中，重新定義 ENVIRONMENT 常數來改變，詳情請參閱 :doc:`安全性 <../general/security>` 的章節。 

例如:

-define       define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
-define       define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'testing');
-define       define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');

如果您是 CodeIgniter 的初學者，請參閱 :doc:`入門指南 <../overview/getting_started>` 的章節，來學習如何建置動態 PHP 應用程式。

.. toctree::
	:hidden:
	:titlesonly:

	downloads
	self
	upgrading
	troubleshooting
	
