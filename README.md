
# Fizz buzz rest endpoints under drupal 9 and docker

  ## Requirement :
  - Docker 
  - Docker-composer
  - Cygwin ( windows only )
 

## project installation :
### Run the commands below : 
1 - make up

2 - make composer install

3 - make drush sql-cli < backup.sql

4 - make drush cim

### last step :
 add this entry "127.0.0.1       fizzbuzz.docker.localhost" to your hosts file . 
  
  

## endpoints description :

  
### Hits endpoint  :
#####  http://fizzbuzz.docker.localhost:8000/api/fizz-buzz-endpoint-hits
no parameters needed .
### Fizz buzz endpoint : 
##### http://fizzbuzz.docker.localhost:8000/api/fizz-buzz?var1=3&var2=7&var3=44444&str1=fizz&str2=buzz&_format=json
required parameters : var1 (interger) , var2(integer) , var3(interger) , str1(string) , str2(string) 
