resource "google_compute_instance" "host" {
  name         = "${var.environment}-host"
  machine_type = "e2-medium"
  zone         = var.zone

  boot_disk {
    initialize_params {
      image = "ubuntu-os-cloud/ubuntu-2204-lts"
      size  = 20
    }
  }

  network_interface {
    subnetwork = google_compute_subnetwork.subnet.name

    access_config {
      // Assign a public IP
    }

  }

  metadata = {
    ssh-keys = "host-${var.environment}:${file(var.ssh_public_key)}"
  }

  metadata_startup_script = <<-EOF
    #!/bin/bash
    set -e

    # Update package list and install prerequisites
    sudo apt-get update
    sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common

    # Add Docker's official GPG key
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

    # Add Docker's APT repository
    sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"

    # Update package list again and install Docker
    sudo apt-get update
    sudo apt-get install -y docker-ce

    # Add the user to the docker group
    sudo usermod -aG docker host-${var.environment}

    # Enable and start Docker service
    sudo systemctl enable docker
    sudo systemctl start docker
  EOF
}

resource "google_compute_firewall" "compute_firewall" {
  name    = "${var.environment}-firewall"
  network = google_compute_network.vpc.name

  allow {
    protocol = "tcp"
    ports    = ["80", "443"]
  }

  source_ranges = ["0.0.0.0/0"]
}