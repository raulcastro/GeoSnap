/* resources/js/app.js */

import './bootstrap';
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

// Initialize Dropzone
Dropzone.autoDiscover = false;

const photoDropzone = new Dropzone("#photo-dropzone", {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 20, // MB
    acceptedFiles: 'image/*',
    dictDefaultMessage: 'Drag and drop a photo here or click to upload',
});
