variable "project_id" {
  description = "GCP Project ID"
  type        = string
}

variable "region" {
  description = "GCP Region"
  type        = string
}

variable "zone" {
  description = "GCP Zone"
  type        = string
}

variable "credentials_file" {
  description = "The path to the GCP credentials file"
  type        = string
}

variable "image_os" {
  description = "The OS image to use for the instances"
  type        = string
}

variable "ssh_public_key" {
  description = "The SSH public key for accessing instances"
  type        = string
}