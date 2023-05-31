# prestashop Checkout module healthyinfocheckout

checkoutModule for PrestaShop is an open source plugin that links e-commerce websites based on PrestaShop to checkout service developed by Tess

## Prerequisit

Module built for version 1.6 and v.1.7 (backward compatibility) To install, please insure to run a version listed above on your back office Check on top of screen besides the prestashop logo ! Or in Menu / Advanced parameters / informations)

## Installation

To install the module, follow these steps:

- Goto `Modules > Module manager` menu in PrestaShop Back Office.
- Click on `Upload a module` button and upload healthyinfochekcout module ZIP.
- Once zip file uploaded, module will be part of the module list , click on Install Then "Configure"
- Enter your client id and client secret once valid you should be able to use this module


"client_id": "ZoD3WHH4NDMqmllzwrJejZ0QMVBKp2hg",

"client_secret": "kXeB4DAg4Iax2RPDwjBBETOuOpO9ymSLKBJjg274gvN7vlr-moxBECKoCPpyw5cD",

![t1](https://user-images.githubusercontent.com/3927152/224308715-3b0aafcb-886c-4b7b-9ba3-6e10d82c1f5e.png)
![t2](https://user-images.githubusercontent.com/3927152/224308737-d849a0b9-b109-419e-91f4-f3127facafd3.png)

- Goto `Design > Position > displayPersonalInformationTop` to make sure HealthyQ had show up in this hook position
  ![pp](https://user-images.githubusercontent.com/3927152/224308772-7c146292-391e-4c91-9f87-72642a77ac1c.png)

## Configuration
Once auth pass, you could go to Advance Parameters > HealthyQ
![2](https://github.com/tesshsu/healthyinfocheckout/assets/3927152/ffe6d13f-6914-406a-9087-0bb013c7a577)
![1](https://github.com/tesshsu/healthyinfocheckout/assets/3927152/446374c6-b313-4e52-92ee-c42ec33bdd43)


## USAGE & REQUIREMENTS

/!\ IMPORTANT : Once install again older version will be deleted and your configuration client_id and client_secret will be reset.

/!\ IMPORTANT : The healthy info data will stock in DBB once submit to checkout payment valid

Go to checkout order page as client and click one of healthy question
![2](https://user-images.githubusercontent.com/3927152/224308967-8d07b363-15b9-49a3-8bb6-415ea8e3f31d.png)

Once finish order go to admin dashboard to check the order should have client updated information

![55](https://user-images.githubusercontent.com/3927152/224309157-36a14262-dbf3-4a42-8125-70068de40ccf.png)






