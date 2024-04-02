<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Gen extends Model
{
    use HasFactory;
    // function for generating Model
    public function gen_model()
    {

        // we  get the columns in the table
        $table_cols = Schema::getColumnListing($this->table_name);
        $variables = $this->makeVars($table_cols);
        $fromJson = Gen::fromJsons($table_cols);
        $toJson = Gen::to_Jsons($table_cols);
        $sqlTableVars = Gen::sqlTableVars($table_cols);
        $x = <<<EOT
        <pre>
        import 'dart:async';
        import 'package:flutter/material.dart';
        import 'package:invetotrack/Models/ResponseModel.dart';
        import 'package:sqflite/sqflite.dart';
        import 'Utils.dart';

        class $this->class_name{
            static String end_point = "{$this->end_point}";
            static String tableName = "{$this->table_name}";
            $variables
         // tojson
            Map&ltString, dynamic&gt toJson() {
                return {
             {$toJson}
            };

            }

             // items functions
             

            static Future&ltList&lt$this->class_name&gt&gt get_items(
                {String where = ""}) async {
              List&lt$this->class_name&gt items = await get_local_items();
              if (items.isEmpty) {
                await get_online_items();
                items = await get_local_items();
              } else {
                await get_online_items();
              }
          
              items = await get_local_items();
              print("====found \${items.length} items in local db");
          
              return items;
            }

            // get online data


            static Future&ltvoid&gt get_online_items() async {
                if (!(await Utils.isConnected())) {
                  return;
                }
                ResponseModel? resp = null;
                try {
                  resp = ResponseModel(await Utils.http_get('api/\$end_point', {}));
                } catch (e) {
                  resp == null;
                }
                if (resp == null) {
                  print('FAILED TO FETCH DATA');
                  return;
                }
                if (resp.code != 1) {
                  Utils.toast(
                    "Failed to fetch \$tableName data.\${resp.message}",
                    c: Colors.red,
                  );
                  return;
                }
                
                try {
                  await delete_all();
                } catch (e) {
                  print("Failed to delete all \$tableName data \${e.toString()}");
                  Utils.toast("Failed to delete all \$tableName data \${e.toString()}",
                      c: Colors.red);
                }
                
            
                
                if (!(resp.data.runtimeType.toString().toLowerCase().contains('list'))) {
                  Utils.toast("failed to fetch \$tableName data.\${resp.data}",
                      c: Colors.red);
                  return;
                }
            
            
                Database db = await Utils.getDb();
               
                String tamp_resp = await initTable(db);
                if (!tamp_resp.isEmpty) {
                  Utils.toast("failed to init table \$tableName. \$tamp_resp,",
                      c: Colors.red);
                  return;
                }
                await db.transaction((transaction) async {
                  var batch = transaction.batch();
                  
                  for (var x in resp?.data) {
                    $this->class_name item = $this->class_name.fromJson(x);
                   
                    try {
                      batch.insert(tableName, item.toJson(),
                          conflictAlgorithm: ConflictAlgorithm.replace);
                    } catch (e) {
                      Utils.toast("Failed to save data \$tableName data.\${e.toString()}",
                          c: Colors.red);
                    }
                  }
                 
                  try {
                    await batch.commit(continueOnError: true);
                    print("successful \$tableName data .\${batch.length}");
                  } catch (e) {
                    Utils.toast("Failed to commit \$tableName data.\${e.toString()}",
                        c: Colors.red);
                  }
                });
              }


            
              static fromJson(dynamic m){
                $this->class_name obj = new $this->class_name();
                if(m == null){
                    return obj;
                }
                $fromJson
                return obj;
            }


          // get online items

            static Future&ltList&lt{$this->class_name}&gt&gt get_local_items(
                {String where = "1"}) async {
              List&lt{$this->class_name}&gt data = [];
              Database db = await Utils.getDb();
              if (!db.isOpen) {
                Utils.toast("failed to open database.", c: Colors.red);
                return data;
              }
              String table_resp = await initTable(db);
              if (!table_resp.isEmpty) {
                Utils.toast("failed to Initialise table.{$this->table_name}. \$table_resp",
                    c: Colors.red);
                return data;
              }
              List&ltMap&gt maps = await db.query(tableName, orderBy: 'id DESC');
              if (maps.isEmpty) {
                return data;
              }
              List.generate(maps.length, (i) {
                data.add({$this->class_name}.fromJson(maps[i]));
              });
              return data;
            }

            //delete all

            static Future&ltString&gt delete_all() async {
                Database db = await Utils.getDb();
                if (!db.isOpen) {
                  return 'failed to open database';
                }
                try {
                  await db.delete(tableName);
                  return '';
                } catch (e) {
                  return "failed to delete table because \${e.toString()}";
                }
              }
            

            // initiating table
            static Future&ltString&gt initTable(Database db) async {
                if (!db.isOpen) {
                  Utils.toast("failed to open database", c: Colors.red);
                  return 'failed to open database';
                }
            
                String sql = 'CREATE TABLE IF NOT EXISTS \${tableName} ('
                    $sqlTableVars
                    ')';
                try {
                  await db.execute(sql);
                  return "";
                } catch (e) {
                  return "failed to create table \${tableName} \${e.toString()}";
                }
            }


          

          // function for deleting an item

            Future&ltString&gt delete() async {
                Database db = await Utils.getDb();
                if (!db.isOpen) {
                  return "Failed to open Database";
                }
                try {
                  await db.delete(tableName, where: "id=?", whereArgs: [id]);
                  return "";
                } catch (e) {
                  return "Failed to delete items because \${e.toString()}";
                }
              

              
            }
           

            // function for deleting an item statically

            static Future&ltString&gt delete_item(int id) async {
                Database db = await Utils.getDb();
                if (!db.isOpen) {
                  return "Failed to open Database";
                }
                try {
                  await db.delete(tableName, where: "id=?", whereArgs: [id]);
                  return "";
                } catch (e) {
                  return "Failed to delete items because \${e.toString()}";
                }
            }
              
            //function for saving
        
            Future&ltString&gt save() async {
                Database db = await Utils.getDb();
                String table_results = await initTable(db);
                if (table_results.isNotEmpty) {
                  return table_results;
                }
                try {
                  await db.insert(tableName, toJson());
                } catch (e) {
                  return "Failed to save items becausee \${e.toString()}";
                }
                return "";
              }
            
       
            
            


           
                
              
           
           
            
            
        }



        </pre>
        EOT;
        echo $x;
        die();
    }
    //function for creating the variables from the table columns
    public function makeVars($table_cols)
    {
        $_data = "";
        $i = 0;
        $done = [];
        foreach ($table_cols as $v) {
            $key = trim($v);
            if (strlen($key) < 1) {
                continue;
            }
            if (in_array($key, $done)) {
                continue;
            }
            if ($key == 'id') {
                $_data .= "int {$key} = 0;<br>";
            } else {
                $_data .= "String {$key} = \"\";<br>";
                if (str_contains($key, '_id')) {
                    $key = str_replace('_id', '_text', $key);
                    $_data .= "String {$key} = \"\";<br>";
                }
            }
        }
        return $_data;
    }

    // function for converting  from json
    public static function fromJsons($table_cols = [])
    {
        $_data = "";
        foreach ($table_cols as $v) {
            $key = trim($v);
            if (strlen($key) < 1) {
                continue;
            }
            if ($key == 'id') {
                $_data .= "obj.{$key} = Utils.int_parse(m['{$key}']);<br>";
            } else {
                $_data .= "obj.{$key} = Utils.to_str(m['{$key}']);<br>";
                if (str_contains($key, '_id')) {
                    $key = str_replace('_id', '_text', $key);
                    $_data .= "obj.{$key} = Utils.to_str(m['{$key}']);<br>";
                }
            }
        }

        return $_data;
    }

    // function for converting  to json
    public static function to_Jsons($recs = [])
    {
        $_data = "";
        foreach ($recs as $v) {
            $key = trim($v);
            if (strlen($key) < 2) {
                continue;
            }
            $_data .= " '$key' : $key,<br> ";
            if (str_contains($key, '_id')) {
                $key = str_replace('_id', '_text', $key);
                $_data .= "'$key': $key,<br>";
            }
        }

        return $_data;
    }

    // function for creating the sql for the table initialisation
    public static function sqlTableVars($table_cols = [])
    {
        $_data = "";
        $done = [];
        foreach ($table_cols as $v) {
            $key = trim($v);
            if (strlen($key) < 1) {
                continue;
            }
            if (in_array($key, $done)) {
                continue;
            }
            $done[] = $key;
            if ($key == 'id') {
                $_data .= "\"{$key} INTEGER PRIMARY KEY\"<br>";
            } else {
                $_data .= "\",{$key} TEXT\"<br>";
                if (str_contains($key, '_id')) {
                    $_key = str_replace('_id', '_text', $key);
                    $_data .= "\",{$_key} TEXT\"<br>";
                }
            }
        }

        return $_data;
    }
}
