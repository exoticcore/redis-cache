resource "google_compute_network" "jenkins_vpc" {
  name                    = "jenkins-vpc"
  auto_create_subnetworks = false
}

resource "google_compute_subnetwork" "jenkins_subnet" {
  name          = "jenkins-subnet"
  ip_cidr_range = "10.0.1.0/24"
  network       = google_compute_network.jenkins_vpc.name
  region        = var.region
}

resource "google_compute_subnetwork" "bastion_subnet" {
  name          = "bastion-subnet"
  ip_cidr_range = "10.0.2.0/24"
  network       = google_compute_network.jenkins_vpc.name
  region        = var.region
}