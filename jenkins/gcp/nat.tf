resource "google_compute_router" "jenkins_router" {
  name    = "jenkins-router"
  region  = var.region
  network = google_compute_network.jenkins_vpc.id
}

resource "google_compute_router_nat" "jenkins_nat" {
  name                               = "jenkins-nat"
  router                             = google_compute_router.jenkins_router.name
  region                             = var.region
  nat_ip_allocate_option             = "AUTO_ONLY" # ใช้ External IP อัตโนมัติ
  source_subnetwork_ip_ranges_to_nat = "ALL_SUBNETWORKS_ALL_IP_RANGES"

  log_config {
    enable = true
    filter = "ERRORS_ONLY"
  }
}