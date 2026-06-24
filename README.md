1. Create key pair
EC2 → Key Pairs → Create key pair
Name: assignment-key → choose RSA |.pem
Download to C:\Users\User\Downloads\

------------------------------------------------------------------------------

2. In AWS search CloudFormation
Create stack
Specify template → Upload a template file
Upload a template file
Download this file from Github → iac/template.yaml

Stack name: food-ordering
KeyName: assignment-key
DBPassword: Admin123456!

After create complete
Wait about 5++ minute
------------------------------------------------------------------------------

5. Open website: 
admin | password
