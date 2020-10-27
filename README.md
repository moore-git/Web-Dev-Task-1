# Web Developer Exercise 1

Boston Seeds recently acquired an ecommerce business, and would like to import the customers into its existing bostonseeds.com database for digital marketing purposes. A small portion of the database table has been included in this exercise, but there’s an issue with the data. 

The old website used MD5 hashes on users’ salted passwords, but the bostonseeds.com website uses Bcrypt to store these more securely. Due to the size of the database, it is not practical to contact all of the new customers and have them reset their password, and we want to avoid issuing new passwords as there is a high likelihood that email addresses may be out of date. Instead, we need a solution to convert their old passwords to the more secure Bcrypt format.  

Please write a concise solution that will update the passwords wherever possible. Having analysed the old ecommerce site’s code, **we know that the MD5 is a hash of the plaintext password, with the salt appended to it**. 

In order to simplify the exercise, please note that the following are considered outside the scope of this exercise:

- The code you generate does not need to be unit testable.
- Logging of any kind
- It is not required to conduct this exercise in an object-oriented fashion.
- Password reset functionality should not be included, and there is no requirement to add a "remember" feature. 

**Time allowance: 30-60 minutes**

Please fork or clone this repository to carry out the exercise. You will need to install front-end dependencies using NPM. Please do not spend longer than the time allowance above, and make all code available on Github. 