terraform init
terraform --apply -var-file=terraform.dev.tfvars # Deploy Dev
terraform --apply -var-file=terraform.prod.tfvars # Deploy Prod

#backend
terraform init -backend-config="key=state/dev/terraform.tfstate"
terraform apply -var-file=terraform.dev.tfvars

terraform init -backend-config="key=state/prod/terraform.tfstate"
terraform apply -var-file=terraform.prod.tfvars
