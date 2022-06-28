<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The comments block helper functions and callbacks
 *
 * @package   local_catimage
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_local_catimage_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2020061501) {

         // Add auth table.
    $table = new xmldb_table('local_category_image');

    // Add fields.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('category_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
    $table->add_field('image', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('createtime', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    

    // Add keys and index.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    

    // Create table if it does not exist.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
        // Seatcreation savepoint reached.
        upgrade_plugin_savepoint(true, 2020061501, 'local','catimage');
        } 

        
    
    return true;
}