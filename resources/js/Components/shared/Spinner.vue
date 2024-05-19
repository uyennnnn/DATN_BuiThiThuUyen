<script>
  import { VueSpinner } from 'vue3-spinners';
  
  export default {
    components: {
      VueSpinner,
    },
    props: {
      spinnerSize: {
        type: Number,
        default: 160
      },
      spinnerColor: {
        type: String,
        default: '#286fee'
      },
      text: {
        type: String,
        default:'データ生成中'
      }
    }
  };
  </script>

<template>
  <div class="spinner-container">
    <div class="custom-spinner">
      <VueSpinner :size="spinnerSize" :color="spinnerColor"/>
    </div>
    <div class="spinner-text">{{ text }}</div>
  </div>
</template>
  
  
  <style>
  .spinner-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
  }

  .custom-spinner .path {
    stroke-width: 1; 
   }

  .spinner-text {
    position: absolute;
    color: #286fee; 
    font-size: 1rem; 
    z-index: 1; 
  }
  </style>
  