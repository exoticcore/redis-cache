resource "google_compute_instance" "bastion_host" {
  name         = "bastion-host"
  machine_type = "e2-micro"
  zone         = var.zone

  scheduling {
    preemptible       = true
    automatic_restart = false
  }

  boot_disk {
    initialize_params {
      image = var.image_os
    }
  }

  network_interface {
    subnetwork = google_compute_subnetwork.bastion_subnet.name

    access_config {
      // Assign a public IP
    }
  }

  metadata = {
    ssh-keys = "bastion:${file(var.ssh_public_key)}"
  }
}

resource "google_compute_firewall" "bastion_firewall" {
  name    = "bastion-firewall"
  network = google_compute_network.jenkins_vpc.name

  allow {
    protocol = "tcp"
    ports    = ["22"]
  }

  source_ranges = ["0.0.0.0/0"]
}