role :app, %w{ec2-user@appraiserpal.com}

require './config/myconfig.rb'

set :deploy_to, '/var/www/capistrano/'

components_dir = '/var/www/capistrano/components'
set :components_dir, components_dir

namespace :deploy do

  desc 'Get stuff ready prior to symlinking'
  task :compile_assets do
    on roles(:app), in: :sequence, wait: 1 do
      execute "cp #{components_dir}/prod.env #{release_path}/.env"
      execute "cp -r #{components_dir}/vendor #{release_path}/vendor"
      execute "cp -r #{components_dir}/public #{release_path}"
    end
  end

  after :updated, :compile_assets

  desc "Build"
  after :updated, :build do
      on roles(:app) do
          within release_path  do
              execute :chmod, "u+x artisan" # make artisan executable

              # Make artisan executable and run optimization
              execute :chmod, "u+x #{release_path}/artisan"
#                 execute :php, "#{release_path}/artisan optimize"
              execute :php, "#{release_path}/artisan down"
              execute :php, "#{release_path}/artisan cache:clear"
              execute :php, "#{release_path}/artisan view:clear"
              execute :php, "#{release_path}/artisan config:clear"
#               execute :php, "#{release_path}/artisan config:cache"
              execute :php, "#{release_path}/artisan migrate"

              # Set full access for updatable directories
              execute :chmod, " -R 0777 #{release_path}/storage"
              execute :chmod, " -R 0777 #{release_path}/bootstrap"
              execute :chmod, " -R 0777 #{release_path}/public"
          end
      end
  end

  desc 'Restart php fpm'
  task :restart_fpm do
    on roles(:app), in: :sequence, wait: 1 do
        execute "sudo /etc/init.d/nginx restart"
        execute "sudo service php-fpm restart"
        execute "sudo service nginx restart"
        execute :php, "#{release_path}/artisan up"
    end
  end
end

# Devops commands
namespace :ops do

  desc 'Copy non-git ENV specific files to servers.'
  task :put_env_components do
    on roles(:app), in: :sequence, wait: 1 do
      upload! './prod.env', "#{components_dir}"
    end
  end

  desc 'Copy non-git files to servers.'
  task :put_components do
    on roles(:app), in: :sequence, wait: 1 do
      system("tar -zcf ./build/vendor.tar.gz ./vendor ")
      upload! './build/vendor.tar.gz', "#{components_dir}", :recursive => true
      execute "cd #{components_dir}
      tar -xzf vendor.tar.gz"

      system("tar -czvf ./build/build-scripts.tar.gz ./public/build ./public/customer ./public/admin ./public/worker ./public/styles")
      upload! './build/build-scripts.tar.gz', "#{components_dir}", :recursive => true
      execute "cd #{components_dir}
      tar -xzf build-scripts.tar.gz"

      system("tar -czvf ./public/files.tar.gz ./public/files")
      upload! './public/files.tar.gz', "#{components_dir}", :recursive => true
      execute "cd #{components_dir}
      tar -xzvf files.tar.gz"
    end
  end

end