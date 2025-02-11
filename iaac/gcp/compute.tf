resource "google_compute_instance" "host" {
  name         = "${var.environment}-host"
  machine_type = "e2-micro"
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