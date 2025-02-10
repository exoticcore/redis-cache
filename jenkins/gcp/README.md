# Generate SSH Key first

ssh-keygen -t rsa -b 2048 -f ~/.ssh/jenkins-key

# SSH into Bastion host

ssh -i ~/.ssh/my-jenkins bastion@<bastion-public-ip>

# From bastion ssh into Jenkins server

ssh -i ~/.ssh/my-jenkins-key jenkins@<jeknkins-private-ip>

# SSH Tunnel

ssh -i ~/.ssh/my-jenkins-key -L 8080:JENKINS_IP:8080 jenkins@BASTION_IP
