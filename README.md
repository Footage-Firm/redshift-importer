# Amazon Redshift import tool
This tool will take a CSV file and transform it into a format that Redshift won't choke on, based on string replacements.

It will then upload it to Amazon S3 and then execute the `COPY` command on Redshift to import the file.

## Requirements
* php-pgsql

### Installing on Mac with Homebrew:
```bash
brew reinstall php55 --with-postgresql
```

## Installing
* Clone this repository
* Run composer install
* Configure credentials by copying `.env.example` to `.env` and setting the different values

## Using
The import tool expects to files:
* A CSV file that contains data for the table
* A SQL file that contains the `CREATE TABLE` statement, describing the table structure of the file

The files has to be named the same with `csv` and `sql` extensions respectively.

**Example:**
```bash
$ ls
abtests.csv	abtests.sql

$ head -n 2 abtests.csv
master_id,site_id,id,"test_name","test_description","start_date","end_date","update_end_date","experience_end_date",is_active,assign_new_members,assign_existing_members,assign_freetrial_new_members,assign_freetrial_step1_members,assign_freetrial_step1_visits,assign_join_step1_members,assign_join_step1_visits,assign_join_step0_visits,even_split_across_segments,"additional_notes"
5372145,2,5,"Mobile_Step1_Short_Form_v4","Vesion 4 - Allowing mobile users to sign up with only name and email","2014-01-10 09:40:00","2014-07-19 18:10:35","0000-00-00 00:00:00","0000-00-00 00:00:00",0,0,0,0,0,0,0,0,0,0,NULL

$ head -n 2 abtests.sql
CREATE TABLE `abtests` (
  `master_id` int(10) NOT NULL AUTO_INCREMENT,

$ ./import abtests.csv
Converting file... DONE
Uploading file... DONE
Importing file... DONE
```
