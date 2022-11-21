set :application, 'appraiserpal'
set :scm, "git"
set :repo_url, 'ssh://git@code.tmake.mx:7999/ap/appraiserpal.git'

# run with:
# cap staging ops:put_env_components - upload .env - file
# cap staging ops:put_components  - upload vendor, public scripts
# cap staging deploy BRANCH=feature/file-management
# cap staging deploy:restart_fpm
# or
# cap production ops:put_env_components - upload .env - file
# cap production ops:put_components  - upload vendor, public scripts
# cap production deploy BRANCH=feature/file-management
# cap production deploy:restart_fpm
set :branch, ENV['BRANCH'] if ENV['BRANCH']