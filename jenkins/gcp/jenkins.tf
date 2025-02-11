resource "google_compute_instance" "jenkins_server" {
  name         = "jenkins-server"
  machine_type = "e2-medium"
  zone         = var.zone

  scheduling {
    preemptible       = true
    automatic_restart = false
  }

  boot_disk {
    initialize_params {
      image = "ubuntu-os-cloud/ubuntu-2204-lts"
      size  = 20
    }
  }

  network_interface {
    subnetwork = google_compute_subnetwork.jenkins_subnet.name
  }

  metadata = {
    ssh-keys = "jenkins:${file(var.ssh_public_key)}"
  }

  # metadata_startup_script = <<-EOF
  #  #!/bin/bash
  #   set -e

  #   # Log file
  #   exec > /var/log/startup-script.log 2>&1

  #   # Set hostname
  #   sudo hostnamectl set-hostname jenkins-server
  #   echo "127.0.1.1 jenkins-server" | sudo tee -a /etc/hosts

  #   # Set DNS
  #   echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf
  #   echo "nameserver 8.8.4.4" | sudo tee -a /etc/resolv.conf

  #   # Update repository to use main server
  #   sudo sed -i 's|http://asia-southeast1.gce.archive.ubuntu.com/ubuntu|http://archive.ubuntu.com/ubuntu|g' /etc/apt/sources.list

  #   # Install Docker
  #   sudo apt-get update
  #   sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
  #   curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
  #   sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
  #   sudo apt-get update
  #   sudo apt-get install -y docker-ce

  #   # Add Jenkins user to Docker group
  #   sudo usermod -aG docker jenkins

  #   # Enable and start Docker service
  #   sudo systemctl enable docker
  #   sudo systemctl start docker

  #   sudo docker pull jenkins/jenkins:lts

  #   # Pull and run Jenkins Docker container
  #   sudo docker run -d --name jenkins -p 8080:8080 -p 50000:50000 -v jenkins_home:/var/jenkins_home jenkins/jenkins:lts

  # EOF
}

resource "google_compute_firewall" "jenkins_firewall" {
  name    = "jenkins-firewall"
  network = google_compute_network.jenkins_vpc.name

  direction = "INGRESS"

  allow {
    protocol = "tcp"
    ports    = ["22", "8080"]
  }

  source_ranges = ["10.0.2.0/24"] # Allow traffic from bastion host
}

resource "google_compute_firewall" "allow_egress_http_https" {
  name    = "allow-egress-http-https"
  network = google_compute_network.jenkins_vpc.id

  direction = "EGRESS"
  priority  = 1000

  destination_ranges = ["0.0.0.0/0"]

  allow {
    protocol = "tcp"
    ports    = ["80", "443"]
  }
}