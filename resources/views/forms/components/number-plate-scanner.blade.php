<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            cameraActive: false,
            stream: null,
            scanning: false,
            errorMessage: '',

            async startCamera() {
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });

                    this.$refs.video.srcObject = this.stream;
                    this.cameraActive = true;
                    this.errorMessage = '';
                } catch (error) {
                    this.errorMessage = 'Camera access denied or not available: ' + error.message;
                    console.error('Error accessing camera:', error);
                }
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
                this.cameraActive = false;
            },

            async captureAndScan() {
                if (!this.cameraActive || this.scanning) return;

                this.scanning = true;

                try {
                    // Draw video to canvas
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = this.$refs.video.videoWidth;
                    canvas.height = this.$refs.video.videoHeight;
                    context.drawImage(this.$refs.video, 0, 0, canvas.width, canvas.height);

                    // Convert canvas to blob
                    const blob = await new Promise(resolve => {
                        canvas.toBlob(resolve, 'image/jpeg', 0.9);
                    });

                    // Create form data with image
                    const formData = new FormData();
                    formData.append('upload', blob, 'license-plate.jpg');

                    // Send to API via backend proxy
                    const response = await fetch('/scan-license-plate', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('API request failed: ' + response.statusText);
                    }

                    const data = await response.json();

                    if (data.results && data.results.length > 0) {
                        // Set the plate number in the text field
                        this.state = data.results[0].plate;
                        this.stopCamera();
                    } else {
                        this.errorMessage = 'No license plate detected. Please try again.';
                    }
                } catch (error) {
                    this.errorMessage = 'Error scanning plate: ' + error.message;
                    console.error('Error scanning license plate:', error);
                } finally {
                    this.scanning = false;
                }
        }
    }"
        class="space-y-4"
    >
        <!-- Text input for the license plate -->
        <x-filament::input
            x-model="state"
            id="{{ $getId() }}"
            placeholder="License plate number"
            @class([
                'block w-full rounded-lg text-gray-900 shadow-sm border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                'dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:border-primary-500',
            ])
        />

        <!-- Camera controls -->
        <div class="flex flex-col space-y-3">
            <div x-show="!cameraActive">
                <x-filament::button
                    @click="startCamera"
                    icon="heroicon-o-camera"
                >
                    Open Camera
                </x-filament::button>
            </div>

            <!-- Camera view -->
            <div x-show="cameraActive" class="relative">
                <video
                    x-ref="video"
                    autoplay
                    playsinline
                    class="w-full h-auto rounded-lg border border-gray-300 bg-black"
                    style="max-height: 320px;"
                ></video>

                <div class="absolute inset-x-0 top-2 flex justify-center">
                    <div
                        x-show="errorMessage"
                        x-text="errorMessage"
                        class="px-3 py-1 text-sm bg-red-500 text-white rounded-full shadow"
                    ></div>
                </div>

                <div class="mt-2 flex gap-3">
                    <x-filament::button
                        @click="captureAndScan"
                    >
                        <span x-show="!scanning">Scan Plate</span>
                        <span x-show="scanning">Processing...</span>
                    </x-filament::button>

                    <x-filament::button
                        @click="stopCamera"
                        color="danger"
                    >
                        Cancel
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
