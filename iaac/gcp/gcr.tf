resource "google_artifact_registry_repository" "my-repo" {
  location      = var.region
  repository_id = "sc-spark-repository"
  description   = "example repository"
  format        = "DOCKER"
}