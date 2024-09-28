<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Blockly Controlled 3DOF Robotic Arm</title>
    <script src="blockly/blockly_compressed.js"></script>
    <script src="blockly/blocks_compressed.js"></script>
    <script src="blockly/javascript_compressed.js"></script>
    <script src="blockly/msg/en.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        body { display: flex; }
        #blocklyDiv { height: 480px; width: 400px; }
        #threeCanvas { width: 200px; height: 200px; } /* ปรับขนาด */
        #controls { position: fixed; top: 10px; left: 10px; }
    </style>
</head>
<body>
    <div id="blocklyDiv"></div>
   
    <button onclick="executeCode()">Run Code</button>

    <xml id="toolbox" style="display: none">
        <category name="Controls" colour="120">
            <block type="move_joint"></block>
            <block type="delay_block"></block>
        </category>
    </xml>
    <canvas id="threeCanvas" ></canvas>
    <script>
        // ตั้งค่า Blockly
        var workspace = Blockly.inject('blocklyDiv', {
            toolbox: document.getElementById('toolbox')
        });

        // บล็อกสำหรับควบคุมการเคลื่อนไหวของข้อต่อ
        Blockly.defineBlocksWithJsonArray([
            {
                "type": "move_joint",
                "message0": "Move Joint %1 to %2 degrees",
                "args0": [
                    {
                        "type": "field_dropdown",
                        "name": "JOINT",
                        "options": [
                            ["1", "1"],
                            ["2", "2"],
                            ["3", "3"]
                        ]
                    },
                    {
                        "type": "field_number",
                        "name": "ANGLE",
                        "value": 90,
                        "min": 0,
                        "max": 180
                    }
                ],
                "previousStatement": null,
                "nextStatement": null,
                "colour": 120,
                "tooltip": "Move a joint to a specified angle",
                "helpUrl": ""
            },
            {
                "type": "delay_block",
                "message0": "Delay %1 ms",
                "args0": [
                    {
                        "type": "field_number",
                        "name": "DELAY",
                        "value": 1000
                    }
                ],
                "previousStatement": null,
                "nextStatement": null,
                "colour": 210,
                "tooltip": "Add delay in milliseconds",
                "helpUrl": ""
            }
        ]);

        // การแปลงบล็อกเป็น JavaScript
        Blockly.JavaScript['move_joint'] = function(block) {
            var joint = block.getFieldValue('JOINT');
            var angle = block.getFieldValue('ANGLE');
            var code = `moveJoint(${joint}, ${angle});\n`;
            return code;
        };

        Blockly.JavaScript['delay_block'] = function(block) {
            var delay = block.getFieldValue('DELAY');
            var code = `await delay(${delay});\n`;
            return code;
        };

        // Three.js สำหรับการจำลองแขนกล
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('threeCanvas') });
        renderer.setSize(400, 400);

        let joint1 = new THREE.Group();
        let joint2 = new THREE.Group();
        let joint3 = new THREE.Group();

        let base = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.2, 0.5), new THREE.MeshStandardMaterial({ color: 0x00ff00 }));
        joint1.add(base);

        let armSegment1 = new THREE.Mesh(new THREE.BoxGeometry(0.1, 1, 0.1), new THREE.MeshStandardMaterial({ color: 0xff0000 }));
        armSegment1.position.y = 0.5;
        joint1.add(armSegment1);

        let armSegment2 = new THREE.Mesh(new THREE.BoxGeometry(0.1, 1, 0.1), new THREE.MeshStandardMaterial({ color: 0x0000ff }));
        armSegment2.position.y = 0.5;
        joint2.position.y = 1;
        joint2.add(armSegment2);

        let gripper = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.5, 0.1), new THREE.MeshStandardMaterial({ color: 0xffff00 }));
        gripper.position.y = 0.25;
        joint3.position.y = 1;
        joint3.add(gripper);

        joint1.add(joint2);
        joint2.add(joint3);
        scene.add(joint1);

        camera.position.z = 5;
        camera.position.y = 2;

        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(5, 5, 5);
        scene.add(light);

        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
        }
        animate();

        // ฟังก์ชันสำหรับการเคลื่อนไหวของข้อต่อ
        function moveJoint(joint, angle) {
            const radian = angle * (Math.PI / 180);
            if (joint == 1) {
                joint1.rotation.y = radian;
            } else if (joint == 2) {
                joint2.rotation.z = radian;
            } else if (joint == 3) {
                joint3.rotation.z = radian;
            }
        }

        // ฟังก์ชัน delay
        function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        // ฟังก์ชันสำหรับรันโค้ดจาก Blockly
        async function executeCode() {
            const code = Blockly.JavaScript.workspaceToCode(workspace);
            try {
                await eval(code);
            } catch (error) {
                console.error("Error executing code: ", error);
            }
        }
    </script>
</body>
</html>
