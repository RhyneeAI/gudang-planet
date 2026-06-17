#!/usr/bin/env bash
set -euo pipefail

: "${SSH_KEY:?SSH_KEY is required}"

install -m 700 -d ~/.ssh

if [[ "${SSH_KEY}" == -----BEGIN* ]]; then
  printf '%s' "$SSH_KEY" > ~/.ssh/deploy_key
else
  printf '%s' "$SSH_KEY" | tr -d '\n\r ' | base64 -d > ~/.ssh/deploy_key
fi

sed -i 's/\r$//' ~/.ssh/deploy_key
if grep -q '\\n' ~/.ssh/deploy_key; then
  sed -i 's/\\n/\n/g' ~/.ssh/deploy_key
fi

chmod 600 ~/.ssh/deploy_key
ssh-keygen -y -f ~/.ssh/deploy_key > /dev/null

echo "Deploy key fingerprint:"
ssh-keygen -lf ~/.ssh/deploy_key
echo "Public key (must match cPanel authorized key):"
ssh-keygen -y -f ~/.ssh/deploy_key
