set :ssh_options, {
  keys: %w(~/aws/samecoast/keys/dev-ernesto-west1.pem),
  forward_agent: false,
  auth_methods: %w(publickey)
}