1- open git bash in the dir: "C:\xampp\htdocs" .
2- run this command : git clone "https://github.com/SirenaIsmail/UsersFiles_System.git"  .
3- open git bash in dir: "C:\xampp\htdocs\UsersFiles_System"
4- open mysqlAdmin and create databaas with name: "files" .
5- run this commands :
       - composer install
       - php artisan migrate
       - php artisan db:seed
       - php artisan key:generate
       - php artisan passport:install  
       -php artisan serve

 