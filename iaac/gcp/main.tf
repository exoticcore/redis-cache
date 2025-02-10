terraform {
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 6.19.0"
    }
  }
}

provider "google" {
  project     = var.project_id
  region      = var.region
  zone        = var.zone
  credentials = file("/Users/kreckkead/Downloads/sc-spark-d656f5268abe.json")
}