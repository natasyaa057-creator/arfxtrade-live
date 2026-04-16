// Edukasi Analisis Trading - JavaScript Functions
class EdukasiAnalisis {
    constructor() {
        this.currentQuiz = null;
        this.quizQuestions = this.initializeQuizQuestions();
        this.currentQuestionIndex = 0;
        this.score = 0;
        this.userAnswers = [];
    }

    initializeQuizQuestions() {
        return {
            fundamental: [
                {
                    question: "Apa yang dimaksud dengan GDP dalam analisis fundamental?",
                    options: [
                        "Gross Domestic Product - total nilai barang dan jasa yang diproduksi",
                        "General Data Processing - sistem pengolahan data umum",
                        "Global Development Program - program pengembangan global",
                        "Government Data Policy - kebijakan data pemerintah"
                    ],
                    correct: 0,
                    explanation: "GDP (Gross Domestic Product) adalah total nilai barang dan jasa yang diproduksi dalam suatu negara dalam periode tertentu."
                },
                {
                    question: "Bank sentral mana yang mengatur mata uang USD?",
                    options: [
                        "European Central Bank (ECB)",
                        "Bank of England (BOE)",
                        "Federal Reserve (FED)",
                        "Bank of Japan (BOJ)"
                    ],
                    correct: 2,
                    explanation: "Federal Reserve (FED) adalah bank sentral Amerika Serikat yang mengatur mata uang USD."
                },
                {
                    question: "Indikator ekonomi mana yang memiliki dampak paling tinggi terhadap pasar forex?",
                    options: [
                        "Consumer Price Index (CPI)",
                        "Non-Farm Payrolls (NFP)",
                        "Retail Sales",
                        "Industrial Production"
                    ],
                    correct: 1,
                    explanation: "Non-Farm Payrolls (NFP) memiliki dampak sangat tinggi karena menunjukkan kondisi ketenagakerjaan yang mempengaruhi kebijakan moneter."
                }
            ],
            teknikal: [
                {
                    question: "Apa yang dimaksud dengan Moving Average dalam analisis teknikal?",
                    options: [
                        "Rata-rata harga dalam periode tertentu",
                        "Perubahan harga maksimal",
                        "Volume perdagangan rata-rata",
                        "Tingkat volatilitas harga"
                    ],
                    correct: 0,
                    explanation: "Moving Average adalah rata-rata harga dalam periode tertentu yang digunakan untuk mengidentifikasi trend."
                },
                {
                    question: "Level RSI di atas 70 menunjukkan kondisi apa?",
                    options: [
                        "Oversold - harga kemungkinan akan naik",
                        "Overbought - harga kemungkinan akan turun",
                        "Neutral - tidak ada sinyal jelas",
                        "Strong Buy - sinyal beli kuat"
                    ],
                    correct: 1,
                    explanation: "RSI di atas 70 menunjukkan kondisi overbought, dimana harga kemungkinan akan turun."
                },
                {
                    question: "Pola chart 'Head and Shoulders' adalah pola apa?",
                    options: [
                        "Pola kelanjutan bullish",
                        "Pola pembalikan bearish",
                        "Pola konsolidasi",
                        "Pola breakout"
                    ],
                    correct: 1,
                    explanation: "Head and Shoulders adalah pola pembalikan bearish yang menunjukkan kemungkinan penurunan harga."
                }
            ],
            mixed: [
                {
                    question: "Kombinasi analisis fundamental dan teknikal disebut:",
                    options: [
                        "Analisis konfirmasi",
                        "Analisis hibrida",
                        "Analisis komprehensif",
                        "Semua jawaban benar"
                    ],
                    correct: 3,
                    explanation: "Kombinasi analisis fundamental dan teknikal dapat disebut sebagai analisis konfirmasi, hibrida, atau komprehensif."
                },
                {
                    question: "Suku bunga bank sentral yang naik biasanya menyebabkan:",
                    options: [
                        "Mata uang melemah",
                        "Mata uang menguat",
                        "Tidak ada dampak",
                        "Volatilitas menurun"
                    ],
                    correct: 1,
                    explanation: "Kenaikan suku bunga biasanya menyebabkan mata uang menguat karena menarik investasi asing."
                }
            ]
        };
    }

    // Position Size Calculator
    calculatePositionSize() {
        const accountBalance = parseFloat(document.getElementById('accountBalance').value);
        const riskPercentage = parseFloat(document.getElementById('riskPercentage').value);
        const entryPrice = parseFloat(document.getElementById('entryPrice').value);
        const stopLoss = parseFloat(document.getElementById('stopLoss').value);

        if (!accountBalance || !riskPercentage || !entryPrice || !stopLoss) {
            alert('Mohon isi semua field dengan benar!');
            return;
        }

        const riskAmount = accountBalance * (riskPercentage / 100);
        const pipRisk = Math.abs(entryPrice - stopLoss);
        const pipValue = 10; // Standard pip value for major pairs
        const positionSize = (riskAmount / (pipRisk * pipValue)).toFixed(2);

        const result = `
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted">Risk Amount:</small>
                    <div class="fw-semibold">$${riskAmount.toFixed(2)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Pip Risk:</small>
                    <div class="fw-semibold">${(pipRisk * 10000).toFixed(1)} pips</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Position Size:</small>
                    <div class="fw-semibold teks-emas">${positionSize} lots</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Max Loss:</small>
                    <div class="fw-semibold text-danger">$${riskAmount.toFixed(2)}</div>
                </div>
            </div>
        `;

        document.getElementById('positionSizeDetails').innerHTML = result;
        document.getElementById('positionSizeResult').style.display = 'block';
    }

    // Risk Reward Calculator
    calculateRiskReward() {
        const entryPrice = parseFloat(document.getElementById('rrEntryPrice').value);
        const stopLoss = parseFloat(document.getElementById('rrStopLoss').value);
        const takeProfit = parseFloat(document.getElementById('takeProfit').value);
        const positionSize = parseFloat(document.getElementById('positionSize').value);

        if (!entryPrice || !stopLoss || !takeProfit || !positionSize) {
            alert('Mohon isi semua field dengan benar!');
            return;
        }

        const risk = Math.abs(entryPrice - stopLoss);
        const reward = Math.abs(takeProfit - entryPrice);
        const riskRewardRatio = (reward / risk).toFixed(2);
        const pipValue = 10;
        const riskAmount = risk * pipValue * positionSize;
        const rewardAmount = reward * pipValue * positionSize;

        const result = `
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted">Risk (Pips):</small>
                    <div class="fw-semibold">${(risk * 10000).toFixed(1)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Reward (Pips):</small>
                    <div class="fw-semibold">${(reward * 10000).toFixed(1)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Risk/Reward Ratio:</small>
                    <div class="fw-semibold teks-emas">1:${riskRewardRatio}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Risk Amount:</small>
                    <div class="fw-semibold text-danger">$${riskAmount.toFixed(2)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Reward Amount:</small>
                    <div class="fw-semibold text-success">$${rewardAmount.toFixed(2)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Status:</small>
                    <div class="fw-semibold ${riskRewardRatio >= 1 ? 'text-success' : 'text-warning'}">
                        ${riskRewardRatio >= 1 ? 'Good Trade' : 'Poor Risk/Reward'}
                    </div>
                </div>
            </div>
        `;

        document.getElementById('riskRewardDetails').innerHTML = result;
        document.getElementById('riskRewardResult').style.display = 'block';
    }

    // Pivot Point Calculator
    calculatePivotPoints() {
        const high = parseFloat(document.getElementById('pivotHigh').value);
        const low = parseFloat(document.getElementById('pivotLow').value);
        const close = parseFloat(document.getElementById('pivotClose').value);

        if (!high || !low || !close) {
            alert('Mohon isi semua field dengan benar!');
            return;
        }

        const pivot = (high + low + close) / 3;
        const r1 = (2 * pivot) - low;
        const r2 = pivot + (high - low);
        const r3 = high + 2 * (pivot - low);
        const s1 = (2 * pivot) - high;
        const s2 = pivot - (high - low);
        const s3 = low - 2 * (high - pivot);

        const result = `
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted">R3 (Resistance 3):</small>
                    <div class="fw-semibold text-danger">${r3.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">R2 (Resistance 2):</small>
                    <div class="fw-semibold text-danger">${r2.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">R1 (Resistance 1):</small>
                    <div class="fw-semibold text-danger">${r1.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">PP (Pivot Point):</small>
                    <div class="fw-semibold teks-emas">${pivot.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">S1 (Support 1):</small>
                    <div class="fw-semibold text-success">${s1.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">S2 (Support 2):</small>
                    <div class="fw-semibold text-success">${s2.toFixed(4)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">S3 (Support 3):</small>
                    <div class="fw-semibold text-success">${s3.toFixed(4)}</div>
                </div>
            </div>
        `;

        document.getElementById('pivotPointDetails').innerHTML = result;
        document.getElementById('pivotPointResult').style.display = 'block';
    }

    // Fibonacci Calculator
    calculateFibonacci() {
        const high = parseFloat(document.getElementById('fibHigh').value);
        const low = parseFloat(document.getElementById('fibLow').value);

        if (!high || !low) {
            alert('Mohon isi semua field dengan benar!');
            return;
        }

        const range = high - low;
        const fibLevels = {
            '0%': high,
            '23.6%': high - (range * 0.236),
            '38.2%': high - (range * 0.382),
            '50%': high - (range * 0.5),
            '61.8%': high - (range * 0.618),
            '78.6%': high - (range * 0.786),
            '100%': low
        };

        let result = '<div class="row g-2">';
        Object.keys(fibLevels).forEach((level, index) => {
            const value = fibLevels[level];
            const colorClass = level === '0%' || level === '100%' ? 'teks-emas' : 
                              level === '50%' ? 'text-warning' : 'text-info';
            
            result += `
                <div class="col-6">
                    <small class="text-muted">${level}:</small>
                    <div class="fw-semibold ${colorClass}">${value.toFixed(4)}</div>
                </div>
            `;
        });
        result += '</div>';

        document.getElementById('fibonacciDetails').innerHTML = result;
        document.getElementById('fibonacciResult').style.display = 'block';
    }

    // Quiz Functions
    startQuiz(type) {
        this.currentQuiz = type;
        this.currentQuestionIndex = 0;
        this.score = 0;
        this.userAnswers = [];
        
        const questions = this.quizQuestions[type];
        if (!questions || questions.length === 0) {
            alert('Quiz tidak tersedia untuk jenis ini!');
            return;
        }

        this.showQuestion();
    }

    showQuestion() {
        const questions = this.quizQuestions[this.currentQuiz];
        const question = questions[this.currentQuestionIndex];
        
        const quizContent = document.getElementById('quizContent');
        quizContent.innerHTML = `
            <div class="quiz-question">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Pertanyaan ${this.currentQuestionIndex + 1} dari ${questions.length}</h6>
                    <span class="badge bg-emas text-dark">${this.currentQuiz.toUpperCase()}</span>
                </div>
                
                <div class="question-text mb-4">
                    <h5 class="fw-semibold">${question.question}</h5>
                </div>
                
                <div class="options">
                    ${question.options.map((option, index) => `
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="answer" id="option${index}" value="${index}">
                            <label class="form-check-label" for="option${index}">
                                ${option}
                            </label>
                        </div>
                    `).join('')}
                </div>
                
                <div class="d-flex justify-content-between">
                    <button class="btn btn-outline-light" onclick="edukasiAnalisis.previousQuestion()" ${this.currentQuestionIndex === 0 ? 'disabled' : ''}>
                        <i class="fa-solid fa-arrow-left me-2"></i>Sebelumnya
                    </button>
                    <button class="btn btn-emas" onclick="edukasiAnalisis.nextQuestion()">
                        ${this.currentQuestionIndex === questions.length - 1 ? 'Selesai' : 'Selanjutnya'}
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        `;
        
        quizContent.style.display = 'block';
    }

    nextQuestion() {
        const selectedAnswer = document.querySelector('input[name="answer"]:checked');
        if (!selectedAnswer) {
            alert('Pilih jawaban terlebih dahulu!');
            return;
        }

        const answer = parseInt(selectedAnswer.value);
        this.userAnswers.push(answer);
        
        const questions = this.quizQuestions[this.currentQuiz];
        const question = questions[this.currentQuestionIndex];
        
        if (answer === question.correct) {
            this.score++;
        }

        this.currentQuestionIndex++;
        
        if (this.currentQuestionIndex >= questions.length) {
            this.showQuizResults();
        } else {
            this.showQuestion();
        }
    }

    previousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.currentQuestionIndex--;
            this.showQuestion();
        }
    }

    showQuizResults() {
        const questions = this.quizQuestions[this.currentQuiz];
        const percentage = Math.round((this.score / questions.length) * 100);
        
        let grade = '';
        let gradeClass = '';
        if (percentage >= 90) {
            grade = 'Excellent';
            gradeClass = 'text-success';
        } else if (percentage >= 80) {
            grade = 'Good';
            gradeClass = 'text-info';
        } else if (percentage >= 70) {
            grade = 'Fair';
            gradeClass = 'text-warning';
        } else {
            grade = 'Need Improvement';
            gradeClass = 'text-danger';
        }

        const quizContent = document.getElementById('quizContent');
        quizContent.innerHTML = `
            <div class="quiz-results text-center">
                <div class="mb-4">
                    <i class="fa-solid fa-trophy fa-3x teks-emas mb-3"></i>
                    <h4 class="fw-semibold">Quiz Selesai!</h4>
                    <p class="text-secondary">Hasil quiz ${this.currentQuiz.toUpperCase()}</p>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="kartu-gelap p-3">
                            <h6 class="fw-semibold">Skor</h6>
                            <div class="display-6 fw-bold teks-emas">${this.score}/${questions.length}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="kartu-gelap p-3">
                            <h6 class="fw-semibold">Persentase</h6>
                            <div class="display-6 fw-bold ${gradeClass}">${percentage}%</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="fw-semibold ${gradeClass}">${grade}</h5>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-emas" style="width: ${percentage}%"></div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-outline-light" onclick="edukasiAnalisis.startQuiz('${this.currentQuiz}')">
                        <i class="fa-solid fa-redo me-2"></i>Ulangi Quiz
                    </button>
                    <button class="btn btn-emas" onclick="edukasiAnalisis.showDetailedResults()">
                        <i class="fa-solid fa-list me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        `;
    }

    showDetailedResults() {
        const questions = this.quizQuestions[this.currentQuiz];
        let detailedResults = '<div class="detailed-results"><h5 class="fw-semibold mb-3">Detail Jawaban:</h5>';
        
        questions.forEach((question, index) => {
            const userAnswer = this.userAnswers[index];
            const isCorrect = userAnswer === question.correct;
            
            detailedResults += `
                <div class="mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-semibold mb-0">Pertanyaan ${index + 1}</h6>
                        <span class="badge ${isCorrect ? 'bg-success' : 'bg-danger'}">
                            ${isCorrect ? 'Benar' : 'Salah'}
                        </span>
                    </div>
                    <p class="text-secondary small mb-2">${question.question}</p>
                    <div class="mb-2">
                        <small class="text-muted">Jawaban Anda:</small>
                        <div class="fw-semibold ${isCorrect ? 'text-success' : 'text-danger'}">
                            ${question.options[userAnswer]}
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Jawaban Benar:</small>
                        <div class="fw-semibold text-success">
                            ${question.options[question.correct]}
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Penjelasan:</small>
                        <div class="fw-semibold teks-emas small">${question.explanation}</div>
                    </div>
                </div>
            `;
        });
        
        detailedResults += '</div>';
        
        const quizContent = document.getElementById('quizContent');
        quizContent.innerHTML = detailedResults;
    }
}

// Initialize
let edukasiAnalisis;
document.addEventListener('DOMContentLoaded', function() {
    edukasiAnalisis = new EdukasiAnalisis();
});

// Global functions for onclick events
function calculatePositionSize() {
    edukasiAnalisis.calculatePositionSize();
}

function calculateRiskReward() {
    edukasiAnalisis.calculateRiskReward();
}

function calculatePivotPoints() {
    edukasiAnalisis.calculatePivotPoints();
}

function calculateFibonacci() {
    edukasiAnalisis.calculateFibonacci();
}

function startQuiz(type) {
    edukasiAnalisis.startQuiz(type);
}







