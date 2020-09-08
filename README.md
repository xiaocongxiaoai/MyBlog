# MyBlog
一个练手博客(前后端分离)的后端代码及后台管理页面，内带贝叶斯分类算法以及推荐博客算法。
有需求可联系我：
邮箱：313697239@qq.com
QQ:313697239

#运行数据迁移带路径
php artisan migrate --path=MyBlog/database/migrations/tables

#创建控制器带路径
php artisan make:controller Admin/IndexController

#解决本地开发post请求跨域问题
https://www.cnblogs.com/cxx8181602/p/11021817.html

#脚本生成数据获取后台数据
***
说明：获取用户历史浏览记录，用于算法计算出合适用户爱好的博客并推荐 \
自定义php命令：  php artisan make:command GetUserAction\
调用 php artisan GetUserAction \
逻辑在：App\Console\Commands\GetUserAction.php
***


