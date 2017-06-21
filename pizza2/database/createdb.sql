-- Portable script for creating the pizza database
-- on your dev system:
-- mysql -u root -p < dev_setup.sql    
-- mysql -D pizzadb -u root -p < createdb.sql 
--  or, on topcat:
-- mysql -D <user>db -u <user> -p < createdb.sql 
create table pizza_size(
id integer not null auto_increment,
size_name varchar(30) not null,
primary key (id),
unique (size_name));

create table pizza_orders(
id integer not null auto_increment,
room_number integer not null,
size varchar(30) not null,
day integer not null,
status integer not null, -- 1, 2, 3 
primary key(id));

create table toppings(
id integer not null auto_increment,
topping_name varchar(30) not null,
primary key(id),
unique (topping_name));

create table order_topping (
order_id integer not null,
topping varchar(30) not null,
primary key (order_id, topping),
foreign key (order_id) references pizza_orders(id));

create table pizza_sys_tab (
current_day integer not null);

create table inventory(
productid int primary key,
productname varchar(10) not null,
quantity integer not null);

create table undelivered_orders(
orderid integer primary key,
flour_qty integer not null,
cheese_qty integer not null);

insert into inventory values(11,'flour', 100);
insert into inventory values(12,'cheese', 100);

insert into pizza_sys_tab values (1);

-- minimal toppings and sizes: one each
insert into toppings values (1,'Pepperoni');
insert into pizza_size values (1,'small');
