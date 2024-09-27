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
            <block type="controls_if_high_low"></block>
            <block type="int_value"></block>
            <block type="controls_whileUntil"></block>
            <block type="input_block_number"></block>
            <block type="output_block_number"></block>
        </category>
    </xml>

    <button onclick="executeCode()">Run Code</button>
    <button onclick="saveWorkspace()">Save</button>
    <button onclick="loadWorkspace()">Load</button>

    <input type="file" id="fileInput" style="display: none;" onchange="loadFile(event)">

    <p id="arm_ip"><?php echo $arm_ip ?></p>
    <script>
        var ARM_MODE ="JOINT"
        const esp32IP = document.getElementById('arm_ip').innerHTML;
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

        // บล็อกสำหรับรับ Input
// บล็อกสำหรับรับ Input แบบ Number
        Blockly.defineBlocksWithJsonArray([{
            "type": "input_block_number",
            "message0": "Input number %1",
            "args0": [
                {
                    "type": "field_number",
                    "name": "INPUT_VALUE",
                    "value": 0
                }
            ],
            "output": "Number",
            "colour": 160,
            "tooltip": "Receive input value as a number",
            "helpUrl": ""
        }]);


        // บล็อกสำหรับส่ง Output
        Blockly.defineBlocksWithJsonArray([{
            "type": "output_block_number",
            "message0": "Output number %1",
            "args0": [
                {
                    "type": "input_value",
                    "name": "OUTPUT_VALUE",
                    "check": "Number"
                }
            ],
            "previousStatement": null,
            "nextStatement": null,
            "colour": 230,
            "tooltip": "Send output value as a number",
            "helpUrl": ""
        }]);

        // สร้างบล็อก 'if'
        // สร้างบล็อก 'if' ที่ตรวจสอบค่าเป็น HIGH หรือ LOW
        Blockly.defineBlocksWithJsonArray([{
            "type": "controls_if_high_low",
            "message0": "if input %1 is %2 then",
            "args0": [
                {
                    "type": "input_value",
                    "name": "INPUT"
                },
                {
                    "type": "field_dropdown",
                    "name": "STATE",
                    "options": [
                        ["HIGH", "HIGH"],
                        ["LOW", "LOW"]
                    ]
                }
            ],
            "message1": "do %1",
            "args1": [
                {
                    "type": "input_statement",
                    "name": "DO"
                }
            ],
            "previousStatement": null,
            "nextStatement": null,
            "colour": 210,
            "tooltip": "If the input is HIGH or LOW, then do some statements",
            "helpUrl": ""
        }]);

        // การแปลงบล็อก 'if' เป็นโค้ด JavaScript
        Blockly.JavaScript['controls_if_high_low'] = function(block) {
            var input = Blockly.JavaScript.valueToCode(block, 'INPUT', Blockly.JavaScript.ORDER_ATOMIC) || '0';
            var state = block.getFieldValue('STATE');
            
            // ตรวจสอบเงื่อนไขที่เลือก (HIGH หรือ LOW)
            var condition = (state === 'HIGH') ? `${input} === 1` : `${input} === 0`;
            
            var statements_do = Blockly.JavaScript.statementToCode(block, 'DO');
            var code = `if (${condition}) {\n${statements_do}}\n`;
            return code;
        };


        // การแปลงบล็อก Input เป็นโค้ด JavaScript
        Blockly.JavaScript['input_block_number'] = function(block) {
            var inputValue = block.getFieldValue('INPUT_VALUE');
            var code = `${inputValue}`;
            return [code, Blockly.JavaScript.ORDER_ATOMIC];
        };

        // การแปลงบล็อก Output เป็นโค้ด JavaScript
        Blockly.JavaScript['output_block_number'] = function(block) {
            var outputValue = Blockly.JavaScript.valueToCode(block, 'OUTPUT_VALUE', Blockly.JavaScript.ORDER_ATOMIC);
            var code = `console.log(${outputValue});\n`; // คุณสามารถปรับเปลี่ยนโค้ดนี้ได้ตามความต้องการของคุณ
            return code;
        };
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
// สร้างบล็อก Int
        Blockly.defineBlocksWithJsonArray([{
            "type": "int_value",
            "message0": "int %1",
            "args0": [
                {
                    "type": "field_number",
                    "name": "INT",
                    "value": 0
                }
            ],
            "output": "Number",
            "colour": 230,
            "tooltip": "An integer value",
            "helpUrl": ""
        }]);
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
        // การแปลงบล็อก Int เป็นโค้ด JavaScript
        Blockly.JavaScript['int_value'] = function(block) {
            var intValue = block.getFieldValue('INT');
            var code = `${intValue}`;
            return [code, Blockly.JavaScript.ORDER_ATOMIC];
        };
        function executeCode() {
          

            var xml1 = Blockly.Xml.workspaceToDom(workspace);
            var xmlText1 = Blockly.Xml.domToPrettyText(xml1);
            var blockObj = xmlToObj(xmlText1);
            console.log(blockObj);
            executeCommands(blockObj);
        }

        
   

        function sendDelayCommand(delay) {
            setTimeout(() => {
                console.log(`Delay of ${delay} ms completed`);
            }, delay);
        }

        function sendHomePositionCommand() {
            console.log("sendHomePositionCommand");
            console.log(esp32IP);
            fetch(`http://${esp32IP}/homePosition`)
                .then(response => {
                    if (response.ok) {
                        console.log("Moved to Home Position successfully!");
                    } else {
                        console.error("Failed to move to Home Position.");
                    }
                });
        }
        // ฟังก์ชันที่ใช้ในการแปลง XML เป็น Object
function xmlToObj(xmlText) {
    var parser = new DOMParser();
    var xmlDoc = parser.parseFromString(xmlText, "text/xml");

    var workspace = new Blockly.Workspace();
    Blockly.Xml.domToWorkspace(xmlDoc.documentElement, workspace);

    var blocks = workspace.getAllBlocks(false);
    var blockArray = blocks.map(block => blockToObj(block));

    return blockArray;
}

// ฟังก์ชันที่ใช้ในการแปลงแต่ละบล็อกเป็น Object
function blockToObj(block) {
    var obj = {
        type: block.type,
        id: block.id,
        fields: {},
        inputs: {},
        next: null
    };

    // ดึงข้อมูลฟิลด์ของบล็อก
    block.inputList.forEach(input => {
        input.fieldRow.forEach(field => {
            obj.fields[field.name] = field.getValue();
        });
    });

    // ดึงข้อมูลอินพุตของบล็อก
    block.inputList.forEach(input => {
        if (input.connection && input.connection.targetBlock()) {
            obj.inputs[input.name] = blockToObj(input.connection.targetBlock());
        }
    });

    // ดึงข้อมูลบล็อกถัดไป
    if (block.getNextBlock()) {
        obj.next = blockToObj(block.getNextBlock());
    }

    return obj;
}

// ฟังก์ชันสำหรับส่งคำสั่งการเคลื่อนที่ไปยัง ESP32
function sendMoveCommand(theta1, theta2, theta3, speed, acceleration,mode) {
    console.log("sendMoveCommand");
    console.log(esp32IP);
    let txt = `http://${esp32IP}/move?theta1=${theta1}&theta2=${theta2}&theta3=${theta3}&speed=${speed}&acceleration=${acceleration}&mode=${mode}`;
    console.log(txt);


    fetch(txt, {
            method: 'GET',
            mode: 'no-cors'
        }).then(response => {
            console.log("Command sent successfully!");
        }).catch(error => {
            console.error("Failed to send command:", error);
        });

   
}

// ฟังก์ชันสำหรับส่งคำสั่งการควบคุม Gripper ไปยัง ESP32
function sendGripperValueCommand(gripperValue) {

        let  url = `http://${esp32IP}/controlGripper?gripper=${gripperValue}`
        fetch(url, {
            method: 'GET',
            mode: 'no-cors'
        }).then(response => {
            console.log("Command sent successfully!");
        }).catch(error => {
            console.error("Failed to send command:", error);
        });
}
// ฟังก์ชันสำหรับส่งคำสั่งการควบคุม Gripper ไปยัง ESP32
function sendDelayValueCommand(DelayValue) {

let  url = `http://${esp32IP}/delay?time=${DelayValue}`
fetch(url, {
    method: 'GET',
    mode: 'no-cors'
}).then(response => {
    console.log("Command sent successfully!");
}).catch(error => {
    console.error("Failed to send command:", error);
});
}
// ฟังก์ชันสำหรับดำเนินการตาม Object ที่แปลงมาจาก XML
function executeCommands(commandObject) {
    if (!commandObject) return;
    for(let i = 0;i< commandObject.length;i++){
        console.log(commandObject[i]);
           switch (commandObject[i].type) {
        case 'move_arm':
           
            ARM_MODE =commandObject[i].fields.MODE;
            //const { theta1, theta2, theta3, speed, acceleration } = commandObject.values;
            //sendMoveCommand(theta1, theta2, theta3, speed, acceleration);
            break;
        case 'joint_angles':
            console.log(commandObject[i].fields);
            const { THETA1, THETA2, THETA3, SPEED, ACCELERATION } = commandObject[i].fields;
            sendMoveCommand(THETA1, THETA2, THETA3, SPEED, ACCELERATION,ARM_MODE);
            break;
        case 'inverse_kinematics':
            console.log(commandObject[i].fields);
            const { X, Y, Z, t, a } = commandObject[i].fields;
            sendMoveCommand(commandObject[i].fields.X, commandObject[i].fields.Y, commandObject[i].fields.Z, commandObject[i].fields.SPEED, commandObject[i].fields.ACCELERATION,ARM_MODE);
            break;
        case 'delay_block':
            const { DELAY } = commandObject[i].fields;
            sendDelayValueCommand(DELAY);
            return; // หยุดการดำเนินการจนกว่าจะครบเวลาหน่วงเวลา

        case 'gripper_value_control':
            sendGripperValueCommand(commandObject[i].gripper);
            break;

        case 'gripper_on_off_control':
            const gripperValue = commandObject[i].gripperState === "OPEN" ? 0 : 180;
            sendGripperValueCommand(gripperValue);
            break;

        default:
           // console.warn("Unknown command type:", commandObject.type);
    }

    //ดำเนินการบล็อกถัดไป
    executeCommands(commandObject.next);
    }
    
 
}

    </script>
</body>
</html>
