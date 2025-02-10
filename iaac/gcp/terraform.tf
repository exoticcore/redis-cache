terraform {
  required_providers {
    google = {
      source = "hashicorp/google"
      version = "~> 3.5.0"
    }
  }

  backend "gcs" {
    bucket = "sc-spark-terraform-state-bucket"
    prefix = "state/${var.environment}"
    
  }
}