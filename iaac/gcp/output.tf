output "host_public_ip" {
  value       = google_compute_instance.host.network_interface[0].access_config[0].nat_ip
  description = "Public IP address of the Bastion proxy server"
}

output "host_private_ip" {
  value       = google_compute_instance.host.network_interface[0].network_ip
  description = "Private IP address of the Jenkins server"
}


