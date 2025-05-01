<div x-data="camera()" x-init="startCamera()" class="flex flex-col items-center space-y-4">
    <video x-ref="video" autoplay playsinline class="w-full max-w-md rounded shadow-md"></video>

    <canvas x-ref="canvas" class="hidden"></canvas>

    <button @click="scanPlate" type="button" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Scan Plate
    </button>
</div>

<script>
    function camera() {
        return {
            stream: null,
            async startCamera() {
                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error('Camera API not supported.');
                    }
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = this.stream;
                } catch (error) {
                    alert('Error accessing camera: ' + error.message);
                }
            },
            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                    this.$refs.video.srcObject = null;
                }
            },
            async scanPlate() {
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                const context = canvas.getContext('2d');

                // Set canvas size same as video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw the video frame to canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Get base64 image
                const imageBase64 = canvas.toDataURL('image/jpeg').split(',')[1]; // remove "data:image/jpeg;base64,"

                try {
                    const response = await fetch('https://api.platerecognizer.com/v1/plate-reader/', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Token 29d5993998f1b659e66a5ec80cfa55d24b04f11e',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            upload: `data:image/jpeg;base64,${imageBase64}`,
                            regions: ['lk'],
                        }),
                    });

                    const data = await response.json();
                    console.log(data);

                    if (data.results && data.results.length > 0) {
                        const plateNumber = data.results[0].plate.toUpperCase();
                        alert('Plate detected: ' + plateNumber);
                        $wire.set('plate_number', plateNumber);
                    } else {
                        alert('No plate detected.');
                    }
                } catch (error) {
                    alert('Failed to recognize plate: ' + error.message);
                }
            }
        }
    }
</script>
