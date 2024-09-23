<?php
error_reporting(0);
 $arm_ip = $_GET['ip'] ;
if(is_null($arm_ip)){
    $arm_ip = "no_arm";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="blockly/blockly_compressed.js"></script>
    <script src="blockly/blocks_compressed.js"></script>
    <script src="blockly/javascript_compressed.js"></script>
    <script src="blockly/msg/en.js"></script>
</head>
<body>
    <h1>Blockly Robotic Arm Control <?php echo $arm_ip ?></h1>
    <div id="blocklyDiv" style="height: 640px; width: 1000px;"></div>
    <xml id="toolbox" style="display: none">
        <category name="Arm Control" colour="120">
          
            <block type="move_arm"></block>
            <block type="gripper_value_control"></block>
            <block type="gripper_on_off_control"></block>
            <block type="delay_block"></block>
            <block type="home_position"></block>
            <block type="set_zero"></block>
            <block type="joint_angles"></block>
            <block type="inverse_kinematics"></block>
            <block type="controls_for"></block>
            <block type="controls_whileUntil"></block>
        </category>
    </xml>

    <button onclick="executeCode()">Run Code</button>
    <button onclick="saveWorkspace()">Save</button>
    <button onclick="loadWorkspace()">Load</button>

    <input type="file" id="fileInput" style="display: none;" onchange="loadFile(event)">

    <p id="arm_ip"><?php echo $arm_ip ?></p>
    <script>

        const arm_ip = document.getElementById('arm_ip').innerHTML;
        var workspace = Blockly.inject('blocklyDiv', {
            toolbox: document.getElementById('toolbox')
        });
   // ฟังก์ชัน Save Workspace
   function saveWorkspace() {
            var xml = Blockly.Xml.workspaceToDom(workspace);
            var xmlText = Blockly.Xml.domToPrettyText(xml);
            
            var blob = new Blob([xmlText], {type: 'text/xml'});
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'blockly_workspace.xml';
            link.click();
        }

        // ฟังก์ชัน Load Workspace
        function loadWorkspace() {
            document.getElementById('fileInput').click();
        }

        function loadFile(event) {
            var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function(event) {
        var xmlText = event.target.result;
        var xml = Blockly.utils.xml.textToDom(xmlText); // แก้ไขบรรทัดนี้
        Blockly.Xml.domToWorkspace(xml, workspace);
    };
    reader.readAsText(file);
        }


        Blockly.defineBlocksWithJsonArray([
            {
                "type": "move_arm",
                "message0": "Move arm using %1 %2",
                "args0": [
                    {
                        "type": "field_dropdown",
                        "name": "MODE",
                        "options": [
                            ["Joint Angles", "JOINT"],
                            ["Inverse Kinematics", "IK"]
                        ]
                    },
                    {
                        "type": "input_value",
                        "name": "VALUES"
                    }
                ],
                "inputsInline": true,
                "previousStatement": null,
                "nextStatement": null,
                "colour": 120,
                "tooltip": "Move robotic arm with joint angles or inverse kinematics",
                "helpUrl": ""
            }
        ]);
        // บล็อกสำหรับการควบคุม Gripper แบบ Value
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "gripper_value_control",
                "message0": "Set gripper to %1",
                "args0": [
                    {"type": "field_number", "name": "GRIPPER", "value": 0, "min": 0, "max": 180}
                ],
                "previousStatement": null,
                "nextStatement": null,
                "colour": 60,
                "tooltip": "Control the gripper with a specific value",
                "helpUrl": ""
            }
        ]);

        // บล็อกสำหรับการควบคุม Gripper แบบ On/Off
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "gripper_on_off_control",
                "message0": "Set gripper %1",
                "args0": [
                    {
                        "type": "field_dropdown", 
                        "name": "GRIPPER_STATE", 
                        "options": [
                            ["Open", "OPEN"],
                            ["Close", "CLOSE"]
                        ]
                    }
                ],
                "previousStatement": null,
                "nextStatement": null,
                "colour": 60,
                "tooltip": "Control the gripper to open or close",
                "helpUrl": ""
            }
        ]);

        // บล็อกสำหรับการสร้าง delay
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "delay_block",
                "message0": "Delay %1 ms",
                "args0": [
                    {"type": "field_number", "name": "DELAY", "value": 1000}
                ],
                "previousStatement": null,
                "nextStatement": null,
                "colour": 210,
                "tooltip": "Add delay in milliseconds",
                "helpUrl": ""
            }
        ]);

        // บล็อกสำหรับการตั้งค่า Home Position
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "home_position",
                "message0": "Move to Home Position",
                "previousStatement": null,
                "nextStatement": null,
                "colour": 300,
                "tooltip": "Move the robotic arm to the home position",
                "helpUrl": ""
            }
        ]);
        // บล็อกสำหรับการตั้งค่า Zero Position
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "set_zero",
                "message0": "Set to Zero Position",
                "previousStatement": null,
                "nextStatement": null,
                "colour": 330,
                "tooltip": "Set the robotic arm to zero position",
                "helpUrl": ""
            }
        ]);
        // บล็อกสำหรับการทำซ้ำ (For loop)
        Blockly.defineBlocksWithJsonArray([{
            "type": "controls_for",
            "message0": "for %1 from %2 to %3 by %4",
            "args0": [
                {
                    "type": "field_variable",
                    "name": "VAR",
                    "variable": "i"
                },
                {
                    "type": "input_value",
                    "name": "FROM",
                    "check": "Number"
                },
                {
                    "type": "input_value",
                    "name": "TO",
                    "check": "Number"
                },
                {
                    "type": "input_value",
                    "name": "BY",
                    "check": "Number"
                }
            ],
            "previousStatement": null,
            "nextStatement": null,
            "colour": 120,
            "tooltip": "Repeats from a start number to an end number by a specified step.",
            "helpUrl": "",
            "inputsInline": true,
            "extensions": ["contextMenu_newGetVariableBlock"]
        }]);
        // บล็อกสำหรับการทำซ้ำ (While loop)
        Blockly.defineBlocksWithJsonArray([{
            "type": "controls_whileUntil",
            "message0": "while %1",
            "args0": [
                {
                    "type": "input_value",
                    "name": "CONDITION",
                    "check": "Boolean"
                }
            ],
            "previousStatement": null,
            "nextStatement": null,
            "colour": 210,
            "tooltip": "Repeats while a condition is true.",
            "helpUrl": ""
        }]);
        // การแปลงบล็อก 'while loop' เป็นโค้ด JavaScript
        Blockly.JavaScript['controls_whileUntil'] = function(block) {
            var condition = Blockly.JavaScript.valueToCode(block, 'CONDITION', Blockly.JavaScript.ORDER_ATOMIC) || 'false';
            var branch = Blockly.JavaScript.statementToCode(block, 'DO');
            
            var code = `while (${condition}) {\n${branch}}\n`;
            return code;
        };
        // การแปลงบล็อก 'for loop' เป็นโค้ด JavaScript
        Blockly.JavaScript['controls_for'] = function(block) {
            var variable = Blockly.JavaScript.variableDB_.getName(block.getFieldValue('VAR'), Blockly.VARIABLE_CATEGORY_NAME);
            var from = Blockly.JavaScript.valueToCode(block, 'FROM', Blockly.JavaScript.ORDER_ATOMIC) || '0';
            var to = Blockly.JavaScript.valueToCode(block, 'TO', Blockly.JavaScript.ORDER_ATOMIC) || '0';
            var by = Blockly.JavaScript.valueToCode(block, 'BY', Blockly.JavaScript.ORDER_ATOMIC) || '1';
            
            var branch = Blockly.JavaScript.statementToCode(block, 'DO');
            var code = `for (var ${variable} = ${from}; ${variable} <= ${to}; ${variable} += ${by}) {\n${branch}}\n`;
            return code;
        };


        Blockly.JavaScript['gripper_value_control'] = function(block) {
            var gripper = block.getFieldValue('GRIPPER');
            var code = `sendGripperValueCommand(${gripper});\n`;
            return code;
        };

        Blockly.JavaScript['gripper_on_off_control'] = function(block) {
            var gripperState = block.getFieldValue('GRIPPER_STATE');
            var gripperValue = gripperState === "OPEN" ? 0 : 180; // เปิดเป็น 0 และปิดเป็น 180
            var code = `sendGripperValueCommand(${gripperValue});\n`;
            return code;
        };

        Blockly.JavaScript['delay_block'] = function(block) {
            var delay = block.getFieldValue('DELAY');
            var code = `sendDelayCommand(${delay});\n`;
            return code;
        };

        Blockly.JavaScript['home_position'] = function(block) {
            var code = `sendHomePositionCommand();\n`;
            return code;
        };

        // Block for specifying joint angles
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "joint_angles",
                "message0": "Joint 1 %1 Joint 2 %2 Joint 3 %3 speed %4 acceleration %5",
                "args0": [
                    {"type": "field_number", "name": "THETA1", "value": 0},
                    {"type": "field_number", "name": "THETA2", "value": 0},
                    {"type": "field_number", "name": "THETA3", "value": 0},
                    {"type": "field_number", "name": "SPEED", "value": 1000},
                    {"type": "field_number", "name": "ACCELERATION", "value": 500}
                ],
                "output": null,
                "colour": 240,
                "tooltip": "Specify joint angles for movement",
                "helpUrl": ""
            }
        ]);

        // Block for specifying inverse kinematics values
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "inverse_kinematics",
                "message0": "X %1 Y %2 Z %3 speed %4 acceleration %5",
                "args0": [
                    {"type": "field_number", "name": "X", "value": 0},
                    {"type": "field_number", "name": "Y", "value": 0},
                    {"type": "field_number", "name": "Z", "value": 0},
                    {"type": "field_number", "name": "SPEED", "value": 1000},
                    {"type": "field_number", "name": "ACCELERATION", "value": 500}
                ],
                "output": null,
                "colour": 240,
                "tooltip": "Specify position for inverse kinematics",
                "helpUrl": ""
            }
        ]);

        // การแปลงบล็อก 'move_arm' เป็นโค้ด JavaScript
        Blockly.JavaScript['move_arm'] = function(block) {
            var mode = block.getFieldValue('MODE');
            var values = Blockly.JavaScript.valueToCode(block, 'VALUES', Blockly.JavaScript.ORDER_ATOMIC);

            var code = '';
            if (mode === 'JOINT') {
                code = `sendMoveCommand(${values});\n`;
            } else if (mode === 'IK') {
                code = `sendMoveCommand(${values});\n`;
            }
            return code;
        };

        Blockly.JavaScript['joint_angles'] = function(block) {
            var theta1 = block.getFieldValue('THETA1');
            var theta2 = block.getFieldValue('THETA2');
            var theta3 = block.getFieldValue('THETA3');
            var speed = block.getFieldValue('SPEED');
            var acceleration = block.getFieldValue('ACCELERATION');
            var code = `${theta1}, ${theta2}, ${theta3}, ${speed}, ${acceleration}`;
            return [code, Blockly.JavaScript.ORDER_NONE];
        };

        Blockly.JavaScript['inverse_kinematics'] = function(block) {
            var x = block.getFieldValue('X');
            var y = block.getFieldValue('Y');
            var z = block.getFieldValue('Z');
            var speed = block.getFieldValue('SPEED');
            var acceleration = block.getFieldValue('ACCELERATION');
            var code = `${x}, ${y}, ${z}, ${speed}, ${acceleration}`;
            return [code, Blockly.JavaScript.ORDER_NONE];
        };

        function executeCode() {
          

            var xml1 = Blockly.Xml.workspaceToDom(workspace);
            var xmlText1 = Blockly.Xml.domToPrettyText(xml1);
            console.log(xmlText1);
        }

        function sendMoveCommand(theta1, theta2, theta3, speed, acceleration) {
            console.log("sendMoveCommand");
            console.log(arm_ip);
            fetch(`http://${arm_ip}/move?theta1=${theta1}&theta2=${theta2}&theta3=${theta3}&speed=${speed}&acceleration=${acceleration}`)
                .then(response => {
                    if (response.ok) {
                        console.log("Move command sent successfully!");
                    } else {
                        console.error("Failed to send move command.");
                    }
                });
        }

        function sendGripperValueCommand(gripper) {
            console.log("sendGripperValueCommand");
            console.log(arm_ip);
            fetch(`http://${arm_ip}/controlGripper?gripper=${gripper}`)
                .then(response => {
                    if (response.ok) {
                        console.log("Gripper command sent successfully!");
                    } else {
                        console.error("Failed to send gripper command.");
                    }
                });
        }

        function sendDelayCommand(delay) {
            setTimeout(() => {
                console.log(`Delay of ${delay} ms completed`);
            }, delay);
        }

        function sendHomePositionCommand() {
            console.log("sendHomePositionCommand");
            console.log(arm_ip);
            fetch(`http://${arm_ip}/homePosition`)
                .then(response => {
                    if (response.ok) {
                        console.log("Moved to Home Position successfully!");
                    } else {
                        console.error("Failed to move to Home Position.");
                    }
                });
        }
    </script>
</body>
</html>
